<?php

namespace App\Http\Controllers;

use App\Models\BukuBankHeader;
use App\Models\BukuBankDetail;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankBookController extends Controller
{
    /**
     * Display a listing of bank books.
     */
    public function index(Request $request)
    {
        $query = BukuBankHeader::with('proyek');

        if ($request->filled('project')) {
            $query->where('kode_proyek', $request->project);
        }

        if ($request->filled('month')) {
            $query->where('periode_bulan', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('periode_tahun', $request->year);
        }

        $headers = $query->orderBy('periode_tahun', 'desc')
                        ->orderBy('periode_bulan', 'desc')
                        ->paginate(15);
        
        $projects = Proyek::all();

        return view('books.bank.index', [
            'headers' => $headers,
            'projects' => $projects,
        ]);
    }

    /**
     * Display details of a specific bank book.
     */
    public function show($id)
    {
        $header = BukuBankHeader::with(['proyek', 'details' => function($q) {
            $q->orderBy('tanggal', 'asc')->orderBy('id_detail_bank', 'asc');
        }])->findOrFail($id);

        return view('books.bank.show', [
            'header' => $header,
        ]);
    }

    /**
     * Placeholder for Excel Export.
     */
    public function exportExcel(Request $request)
    {
        return back()->with('info', 'Fitur ekspor Excel akan segera diimplementasikan.');
    }
}
