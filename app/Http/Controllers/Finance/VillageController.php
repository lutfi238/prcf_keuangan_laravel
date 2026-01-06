<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    /**
     * Display a listing of villages.
     */
    public function index(Request $request)
    {
        $query = Village::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('village_name', 'like', "%{$search}%")
                  ->orWhere('village_code', 'like', "%{$search}%")
                  ->orWhere('village_abbr', 'like', "%{$search}%");
            });
        }

        $villages = $query->orderBy('village_name')->paginate(15);

        return view('finance.villages.index', [
            'villages' => $villages,
        ]);
    }

    /**
     * Show the form for creating a new village.
     */
    public function create()
    {
        return view('finance.villages.form', [
            'village' => null,
        ]);
    }

    /**
     * Store a newly created village.
     */
    public function store(Request $request)
    {
        $request->validate([
            'village_code' => 'required|string|max:10|unique:villages,village_code',
            'village_name' => 'required|string|max:100',
            'village_abbr' => 'required|string|max:5|unique:villages,village_abbr',
            'description' => 'nullable|string',
        ]);

        Village::create($request->only(['village_code', 'village_name', 'village_abbr', 'description']));

        return redirect()->route('finance.villages.index')
            ->with('success', 'Desa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified village.
     */
    public function edit(Village $village)
    {
        return view('finance.villages.form', [
            'village' => $village,
        ]);
    }

    /**
     * Update the specified village.
     */
    public function update(Request $request, Village $village)
    {
        $request->validate([
            'village_code' => 'required|string|max:10|unique:villages,village_code,' . $village->id_village . ',id_village',
            'village_name' => 'required|string|max:100',
            'village_abbr' => 'required|string|max:5|unique:villages,village_abbr,' . $village->id_village . ',id_village',
            'description' => 'nullable|string',
        ]);

        $village->update($request->only(['village_code', 'village_name', 'village_abbr', 'description']));

        return redirect()->route('finance.villages.index')
            ->with('success', 'Desa berhasil diupdate.');
    }

    /**
     * Remove the specified village.
     */
    public function destroy(Village $village)
    {
        // Check if village is used in budgets
        if ($village->projectCodeBudgets()->exists()) {
            return back()->with('error', 'Desa tidak dapat dihapus karena masih digunakan dalam budget proyek.');
        }

        $village->delete();

        return redirect()->route('finance.villages.index')
            ->with('success', 'Desa berhasil dihapus.');
    }
}
