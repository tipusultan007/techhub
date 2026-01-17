<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
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
        ]);

        Expense::create($request->all() + ['user_id' => Auth::id()]);

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
        ]);

        $expense->update($request->all());

        return back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
