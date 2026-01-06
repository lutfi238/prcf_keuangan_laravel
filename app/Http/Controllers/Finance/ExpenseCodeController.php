<?php

namespace App\Http\Controllers\Finance;

use App\Models\ExpenseCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpenseCodeController extends Controller
{
    public function index()
    {
        $codes = ExpenseCode::orderBy('code')->paginate(20);
        return view('finance.expense-codes.index', compact('codes'));
    }

    public function create()
    {
        return view('finance.expense-codes.form', ['code' => new ExpenseCode()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:expense_codes,code',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        ExpenseCode::create($request->all());

        return redirect()->route('finance.expense-codes.index')
            ->with('success', 'Expense Code berhasil ditambahkan.');
    }

    public function edit(ExpenseCode $expenseCode)
    {
        return view('finance.expense-codes.form', ['code' => $expenseCode]);
    }

    public function update(Request $request, ExpenseCode $expenseCode)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:expense_codes,code,' . $expenseCode->id,
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $expenseCode->update($request->all());

        return redirect()->route('finance.expense-codes.index')
            ->with('success', 'Expense Code berhasil diupdate.');
    }

    public function destroy(ExpenseCode $expenseCode)
    {
        $expenseCode->delete();
        return redirect()->route('finance.expense-codes.index')
            ->with('success', 'Expense Code berhasil dihapus.');
    }
}
