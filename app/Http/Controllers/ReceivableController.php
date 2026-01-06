<?php

namespace App\Http\Controllers;

use App\Models\BukuPiutangHeader;
use App\Models\BukuPiutangDetail;
use App\Models\Proyek;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    /**
     * Display a listing of receivables.
     */
    public function index(Request $request)
    {
        $query = BukuPiutangHeader::with('proyek');

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

        return view('books.receivables.index', [
            'headers' => $headers,
            'projects' => $projects,
        ]);
    }

    /**
     * Display details of a specific receivable book.
     */
    public function show($id)
    {
        $header = BukuPiutangHeader::with(['proyek', 'details' => function($q) {
            $q->orderBy('tgl_trx', 'asc');
        }])->findOrFail($id);

        return view('books.receivables.show', [
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
