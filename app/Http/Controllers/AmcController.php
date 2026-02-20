<?php

namespace App\Http\Controllers;

use App\Models\Amc;
use App\Models\AmcIncludedService;
use App\Models\AmcItem;
use App\Models\AmcService;
use App\Models\AmcTemplate;
use App\Models\Customer;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AmcController extends Controller
{
    public function index(Request $request)
    {
        $query = Amc::with('customer');

        if ($request->filled('search')) {
            $query->where('contract_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $amcs = $query->latest()->paginate(10)->withQueryString();
        $customers = Customer::orderBy('name')->get();

        return view('admin.amcs.index', compact('amcs', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $templates = AmcTemplate::orderBy('name')->get();

        return view('admin.amcs.create', compact('customers', 'products', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric',
            'frequency' => 'required|in:monthly,quarterly,semi-annually,annually',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'included_services' => 'nullable|array',
            'included_services.*.service_name' => 'required_with:included_services|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $amc = Amc::create([
            'customer_id' => $request->customer_id,
            'contract_number' => 'AMC-'.strtoupper(Str::random(8)),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'amount' => $request->amount,
            'status' => 'active',
            'frequency' => $request->frequency,
            'notes' => $request->notes,
            'custom_agreement_content' => $request->agreement_type === 'custom' ? $request->custom_agreement_content : null,
        ]);

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $amc->addMedia($file)->toMediaCollection('attachments');
            }
        }

        foreach ($request->items as $item) {
            AmcItem::create([
                'amc_id' => $amc->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
            ]);
        }

        $this->generateScheduleLogic($amc);

        return redirect()->route('amcs.index')->with('success', 'AMC created and schedule generated.');
    }

    public function show(Amc $amc)
    {
        $amc->load(['customer', 'items.product', 'services.technician']);

        return view('admin.amcs.show', compact('amc'));
    }

    public function edit(Amc $amc)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $templates = AmcTemplate::orderBy('name')->get();
        $amc->load('items');

        return view('admin.amcs.edit', compact('amc', 'customers', 'products', 'templates'));
    }

    public function update(Request $request, Amc $amc)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric',
            'frequency' => 'required|in:monthly,quarterly,semi-annually,annually',
            'status' => 'required|in:pending,active,expired,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'attachment.*' => 'nullable|file|max:102400',
        ]);

        $amc->update([
            'customer_id' => $request->customer_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'amount' => $request->amount,
            'status' => $request->status,
            'frequency' => $request->frequency,
            'notes' => $request->notes,
            'custom_agreement_content' => $request->agreement_type === 'custom' ? $request->custom_agreement_content : null,
        ]);

        if ($request->hasFile('attachment')) {
            $amc->clearMediaCollection('attachments');
            foreach ($request->file('attachment') as $file) {
                $amc->addMedia($file)->toMediaCollection('attachments');
            }
        }

        // Sync items (simple approach: delete and recreate)
        $amc->items()->delete();
        foreach ($request->items as $item) {
            AmcItem::create([
                'amc_id' => $amc->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
            ]);
        }

        $amc->includedServices()->delete();
        if ($request->has('included_services')) {
            foreach ($request->included_services as $service) {
                if (! empty($service['service_name'])) {
                    AmcIncludedService::create([
                        'amc_id' => $amc->id,
                        'service_name' => $service['service_name'],
                        'description' => $service['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('amcs.index')->with('success', 'AMC updated successfully.');
    }

    public function generateSchedule(Amc $amc)
    {
        // Clear existing scheduled services if any (be careful not to delete completed ones if updating)
        $amc->services()->where('status', 'scheduled')->delete();
        $this->generateScheduleLogic($amc);

        return back()->with('success', 'Service schedule regenerated.');
    }

    private function generateScheduleLogic(Amc $amc)
    {
        $start = Carbon::parse($amc->start_date);
        $end = Carbon::parse($amc->end_date);
        $frequency = $amc->frequency;

        $intervalMonths = match ($frequency) {
            'monthly' => 1,
            'quarterly' => 3,
            'semi-annually' => 6,
            'annually' => 12,
        };

        $currentDate = $start->copy()->addMonths($intervalMonths);

        while ($currentDate <= $end) {
            AmcService::create([
                'amc_id' => $amc->id,
                'scheduled_date' => $currentDate->toDateString(),
                'status' => 'scheduled',
            ]);
            $currentDate->addMonths($intervalMonths);
        }
    }

    public function generateAgreement(Amc $amc, Request $request)
    {
        if ($amc->custom_agreement_content) {
            $content = $this->parseTemplate($amc->custom_agreement_content, $amc);

            return view('admin.amcs.agreement', compact('amc', 'content'));
        }

        $templateId = $request->template_id;
        $template = $templateId
            ? AmcTemplate::find($templateId)
            : AmcTemplate::where('is_default', true)->first() ?? AmcTemplate::first();

        if (! $template) {
            return back()->with('error', 'No agreement template found. Please create one first.');
        }

        $content = $this->parseTemplate($template->content, $amc);

        return view('admin.amcs.agreement', compact('amc', 'content'));
    }

    public function downloadAgreement(Amc $amc, Request $request)
    {
        if ($amc->custom_agreement_content) {
            $content = $this->parseTemplate($amc->custom_agreement_content, $amc);
        } else {
            $templateId = $request->template_id;
            $template = $templateId
                ? AmcTemplate::find($templateId)
                : AmcTemplate::where('is_default', true)->first() ?? AmcTemplate::first();

            $content = $this->parseTemplate($template->content ?? '', $amc);
        }

        $pdf = Pdf::loadView('admin.amcs.pdf', compact('amc', 'content'));

        return $pdf->download("AMC_Agreement_{$amc->contract_number}.pdf");
    }

    private function parseTemplate($content, Amc $amc)
    {
        $itemsTable = '<table border="1" width="100%" style="border-collapse: collapse;"><thead><tr><th>Product</th><th>Description</th></tr></thead><tbody>';
        foreach ($amc->items as $item) {
            $productName = $item->product ? $item->product->name : 'N/A';
            $itemsTable .= "<tr><td>{$productName}</td><td>{$item->description}</td></tr>";
        }
        $itemsTable .= '</tbody></table>';

        $servicesTable = '<table border="1" width="100%" style="border-collapse: collapse;"><thead><tr><th>Visit No.</th><th>Scheduled Date</th><th>Status</th></tr></thead><tbody>';
        foreach ($amc->services as $index => $service) {
            $visitNo = $index + 1;
            $servicesTable .= "<tr><td>{$visitNo}</td><td>{$service->scheduled_date->format('d M Y')}</td><td>{$service->status}</td></tr>";
        }
        $servicesTable .= '</tbody></table>';

        $includedServicesHtml = '<ul style="margin: 0; padding-left: 20px;">';
        foreach ($amc->includedServices as $service) {
            $includedServicesHtml .= "<li><strong>{$service->service_name}</strong>".($service->description ? ": {$service->description}" : '').'</li>';
        }
        $includedServicesHtml .= '</ul>';

        $variables = [
            '{customer_name}' => $amc->customer->name,
            '{customer_phone}' => $amc->customer->phone,
            '{contract_number}' => $amc->contract_number,
            '{start_date}' => $amc->start_date->format('d M Y'),
            '{end_date}' => $amc->end_date->format('d M Y'),
            '{amount}' => number_format($amc->amount, 2),
            '{frequency}' => ucfirst($amc->frequency),
            '{items_table}' => $itemsTable,
            '{services_table}' => $servicesTable,
            '{included_services}' => $includedServicesHtml,
            '{site_name}' => settings('site_name', 'Tech Hub'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $content);
    }
}
