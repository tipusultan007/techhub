<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Traits\LogsActivity;

class ExpenseController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        // 1. Base Query
        $query = Expense::with(['category', 'user'])->latest('date');

        // 2. Filter by Category
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        // 3. Filter by Date Range
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // 4. Paginate with filters
        $expenses = $query->paginate(15)->withQueryString();

        // Data for dropdowns
        $categories = ExpenseCategory::all();

        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'tax_method' => 'required|in:inclusive,exclusive,no_tax',
        ]);

        $amount = $request->amount;
        $taxMethod = $request->tax_method;
        $taxRate = 0.05; // UAE Standard VAT

        if ($taxMethod === 'inclusive') {
            $netAmount = $amount / (1 + $taxRate);
            $taxAmount = $amount - $netAmount;
        } elseif ($taxMethod === 'exclusive') {
            $netAmount = $amount;
            $taxAmount = $amount * $taxRate;
        } else {
            // No Tax / Exempt / Out of Scope
            $netAmount = $amount;
            $taxAmount = 0;
        }

        $expense = Expense::create($request->except('attachment') + [
            'user_id' => Auth::id(),
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
        ]);

        if ($request->hasFile('attachment')) {
            $expense->addMediaFromRequest('attachment')->toMediaCollection('attachment');
        }

        $this->logActivity('Expense', 'Create', "Recorded Expense of {$expense->amount} for category {$expense->category->name}", [
            'expense_id' => $expense->id,
            'amount' => $expense->amount,
            'tax_method' => $taxMethod,
            'tax_amount' => $taxAmount,
            'category' => $expense->category->name,
        ]);

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user']);
        return view('admin.expenses.show', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'tax_method' => 'required|in:inclusive,exclusive,no_tax',
        ]);

        $amount = $request->amount;
        $taxMethod = $request->tax_method;
        $taxRate = 0.05;

        if ($taxMethod === 'inclusive') {
            $netAmount = $amount / (1 + $taxRate);
            $taxAmount = $amount - $netAmount;
        } elseif ($taxMethod === 'exclusive') {
            $netAmount = $amount;
            $taxAmount = $amount * $taxRate;
        } else {
            $netAmount = $amount;
            $taxAmount = 0;
        }

        $expense->update($request->except('attachment') + [
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
        ]);

        if ($request->hasFile('attachment')) {
            $expense->clearMediaCollection('attachment');
            $expense->addMediaFromRequest('attachment')->toMediaCollection('attachment');
        }

        $this->logActivity('Expense', 'Edit', "Updated Expense of {$expense->amount} for category {$expense->category->name}", [
            'expense_id' => $expense->id,
            'amount' => $expense->amount,
            'tax_method' => $taxMethod,
            'tax_amount' => $taxAmount,
            'category' => $expense->category->name,
        ]);

        return back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        // Explicitly clear media to ensure storage files are removed
        $expense->clearMediaCollection('attachment');

        $this->logActivity('Expense', 'Delete', "Deleted Expense of {$expense->amount}", [
            'amount' => $expense->amount,
        ]);

        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
