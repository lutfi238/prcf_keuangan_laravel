<?php

namespace App\Http\Controllers;

use App\Models\ProjectCodeBudget;
use App\Models\Proyek;
use App\Models\Village;
use App\Models\ProposalBudgetDetail;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectCodeBudget::with(['proyek', 'village']);
        
        if ($request->filled('kode_proyek')) {
            $query->where('kode_proyek', $request->kode_proyek);
        }
        if ($request->filled('id_village')) {
            $query->where('id_village', $request->id_village);
        }
        
        $budgets = $query->orderBy('created_at', 'desc')->paginate(20);
        $projects = Proyek::where('status_proyek', '!=', 'cancelled')->get();
        $villages = Village::orderBy('village_name')->get();
        
        return view('finance.budgets.index', compact('budgets', 'projects', 'villages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_proyek' => 'required|string|exists:proyek,kode_proyek',
            'id_village' => 'required|integer|exists:villages,id_village',
            'exp_code' => 'required|string|max:20',
            'currency' => 'required|in:USD,IDR',
            'amount' => 'required|numeric|min:0',
            'exrate' => 'required|numeric|min:1',
        ]);

        $village = Village::findOrFail($request->id_village);
        $placeCode = $request->exp_code . '-' . $village->village_abbr . '-01';
        
        if ($request->currency === 'USD') {
            $budgetUsd = $request->amount;
            $budgetIdr = $request->amount * $request->exrate;
        } else {
            $budgetIdr = $request->amount;
            $budgetUsd = $request->amount / $request->exrate;
        }

        ProjectCodeBudget::updateOrCreate(
            [
                'kode_proyek' => $request->kode_proyek,
                'id_village' => $request->id_village,
                'exp_code' => $request->exp_code,
            ],
            [
                'place_code' => $placeCode,
                'budget_usd' => $budgetUsd,
                'budget_idr' => $budgetIdr,
                'exrate' => $request->exrate,
            ]
        );

        return redirect()->route('finance.budgets.index')
            ->with('success', "Budget berhasil disimpan! Place Code: {$placeCode}");
    }

    public function destroy(ProjectCodeBudget $budget)
    {
        $budget->delete();
        return redirect()->route('finance.budgets.index')
            ->with('success', 'Budget berhasil dihapus.');
    }

    public function getExpCodes(Request $request)
    {
        $kodeProyek = $request->kode_proyek;
        
        if (!$kodeProyek) {
            return response()->json([]);
        }

        $expCodes = ProposalBudgetDetail::whereHas('proposal', function ($q) use ($kodeProyek) {
            $q->where('kode_proyek', $kodeProyek);
        })->select('exp_code')->distinct()->get()->map(function($item) {
            return [
                'exp_code' => $item->exp_code
            ];
        });
        
        return response()->json($expCodes);
    }
}
