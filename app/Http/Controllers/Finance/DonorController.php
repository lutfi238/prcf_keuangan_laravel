<?php

namespace App\Http\Controllers\Finance;

use App\Models\Donor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DonorController extends Controller
{
    public function index()
    {
        $donors = Donor::orderBy('nama_donor')->paginate(15);
        return view('finance.donors.index', compact('donors'));
    }

    public function create()
    {
        return view('finance.donors.form', ['donor' => new Donor()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_donor' => 'required|string|max:20|unique:donors,kode_donor',
            'nama_donor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:50',
            'negara' => 'nullable|string|max:100',
        ]);

        Donor::create($request->all());

        return redirect()->route('finance.donors.index')
            ->with('success', 'Donor berhasil ditambahkan.');
    }

    public function edit(Donor $donor)
    {
        return view('finance.donors.form', compact('donor'));
    }

    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'kode_donor' => 'required|string|max:20|unique:donors,kode_donor,' . $donor->id_donor . ',id_donor',
            'nama_donor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:50',
            'negara' => 'nullable|string|max:100',
        ]);

        $donor->update($request->all());

        return redirect()->route('finance.donors.index')
            ->with('success', 'Donor berhasil diupdate.');
    }

    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->route('finance.donors.index')
            ->with('success', 'Donor berhasil dihapus.');
    }
}
