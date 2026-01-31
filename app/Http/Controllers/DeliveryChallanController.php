<?php

namespace App\Http\Controllers;

use App\Models\DeliveryChallan;
use App\Models\DeliveryChallanItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\LogsActivity;

class DeliveryChallanController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $query = DeliveryChallan::with('customer', 'quotation')
            ->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('challan_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('quotation', function($q) use ($search) {
                    $q->where('quotation_no', 'like', "%{$search}%");
                });
        }

        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $challans = $query->paginate(15);

        return view('admin.delivery_challans.index', compact('challans'));
    }

    public function create(Quotation $quotation)
    {
        // Only show items that have remaining quantity
        $quotation->load('items.product');
        return view('admin.delivery_challans.create', compact('quotation'));
    }

    public function store(Request $request, Quotation $quotation)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:quotation_items,id',
            'items.*.qty' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create Challan Header
            // Generate Sequence: DC-YYYY-00001
            $year = now()->year;
            $lastChallan = DeliveryChallan::whereYear('created_at', $year)->latest()->first();
            $sequence = $lastChallan ? (int)substr($lastChallan->challan_number, -5) + 1 : 1;
            $challanNumber = 'DC-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $challan = DeliveryChallan::create([
                'quotation_id' => $quotation->id,
                'customer_id' => $quotation->customer_id,
                'challan_number' => $challanNumber,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            $hasItems = false;

            foreach ($request->items as $itemData) {
                // Skip if quantity is 0
                if ($itemData['qty'] <= 0) continue;

                $quotationItem = QuotationItem::findOrFail($itemData['id']);

                // Validate quantity
                if ($itemData['qty'] > $quotationItem->remaining_qty) {
                    throw new \Exception("Quantity for {$quotationItem->product_name} exceeds remaining quantity.");
                }

                // Create Challan Item
                DeliveryChallanItem::create([
                    'delivery_challan_id' => $challan->id,
                    'quotation_item_id' => $quotationItem->id,
                    'product_id' => $quotationItem->product_id,
                    'product_name' => $quotationItem->product_name,
                    'quantity' => $itemData['qty'],
                ]);

                // Update Quotation Item Delivered Qty
                $quotationItem->increment('delivered_qty', $itemData['qty']);
                $hasItems = true;
            }

            if (!$hasItems) {
                throw new \Exception("Please select at least one item to deliver.");
            }

            DB::commit();

            $this->logActivity('Delivery Challan', 'Create', "Created Delivery Challan #{$challan->challan_number}", [
                'challan_id' => $challan->id,
                'challan_number' => $challan->challan_number,
            ]);

            return redirect()->route('delivery-challans.show', $challan->id)
                ->with('success', 'Delivery Challan Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $challan = DeliveryChallan::with('items', 'quotation.customer', 'customer')->findOrFail($id);
        return view('admin.delivery_challans.show', compact('challan'));
    }

    public function print($id)
    {
        $challan = DeliveryChallan::with('items', 'quotation.customer', 'customer')->findOrFail($id);
        return view('admin.delivery_challans.print', compact('challan'));
    }

    public function pdf($id)
    {
        $challan = DeliveryChallan::with('items', 'quotation.customer', 'customer')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.delivery_challans.pdf', compact('challan'));
        return $pdf->download('challan-' . $challan->challan_number . '.pdf');
    }

    public function edit($id)
    {
        $challan = DeliveryChallan::with('items.quotationItem', 'quotation')->findOrFail($id);
        return view('admin.delivery_challans.edit', compact('challan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:delivery_challan_items,id', // Validating against challan item ID
            'items.*.qty' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $challan = DeliveryChallan::findOrFail($id);
            $challan->update([
                'date' => $request->date,
                'note' => $request->note,
            ]);

            foreach ($request->items as $itemData) {
                $challanItem = DeliveryChallanItem::findOrFail($itemData['id']);
                $quotationItem = $challanItem->quotationItem;
                
                $oldQty = $challanItem->quantity;
                $newQty = $itemData['qty'];
                $difference = $newQty - $oldQty;

                if ($difference != 0) {
                    // Check availability: (Remaining + Old) must be >= New
                    // effectively: Remaining >= Difference (if difference is positive)
                    if ($difference > 0 && $difference > $quotationItem->remaining_qty) {
                         throw new \Exception("Cannot increase quantity for {$challanItem->product_name}. Exceeds available remaining quantity.");
                    }

                    // Update values
                    $challanItem->update(['quantity' => $newQty]);
                    $quotationItem->increment('delivered_qty', $difference); // increment works for distinct +ve and -ve
                }
            }

            DB::commit();

            $this->logActivity('Delivery Challan', 'Edit', "Updated Delivery Challan #{$challan->challan_number}", [
                'challan_id' => $challan->id,
                'challan_number' => $challan->challan_number,
            ]);

            return redirect()->route('delivery-challans.index')
                ->with('success', 'Delivery Challan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $challan = DeliveryChallan::with('items')->findOrFail($id);

            // Revert the delivered quantities on the quotation
            foreach ($challan->items as $item) {
                $quotationItem = QuotationItem::find($item->quotation_item_id);
                if ($quotationItem) {
                    $quotationItem->decrement('delivered_qty', $item->quantity);
                }
            }

            // Delete the challan (cascading delete should handle items if set up, but let's be safe/explicit if needed, 
            // though usually ON DELETE CASCADE is best. Assuming standard constraints or manual cleanup).
            // Since migration didn't specify cascade for challan items, I should probably rely on model events or delete them.
            // But let's check migration... actually I'll just delete the parent and let DB handle or manually delete.
            // To be safe:
            $challan->items()->delete();
            $challan->delete();

            DB::commit();

            $this->logActivity('Delivery Challan', 'Delete', "Deleted Delivery Challan #{$challan->challan_number}", [
                'challan_number' => $challan->challan_number,
            ]);

            return redirect()->route('delivery-challans.index')
                ->with('success', 'Delivery Challan deleted successfully. Stock reverted to Quotation.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting challan: ' . $e->getMessage());
        }
    }
}