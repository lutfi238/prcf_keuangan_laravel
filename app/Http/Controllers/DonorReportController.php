<?php

namespace App\Http\Controllers;

use App\Models\LaporanDonor;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonorReportController extends Controller
{
    /**
     * Display a listing of donor reports.
     */
    public function index(Request $request)
    {
        $query = LaporanDonor::with(['proyek', 'createdBy']);

        if ($request->filled('project')) {
            $query->where('kode_proyek', $request->project);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        $projects = Proyek::all();

        return view('reports.donor.index', [
            'reports' => $reports,
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new donor report.
     */
    public function create()
    {
        $projects = Proyek::all();
        return view('reports.donor.form', [
            'projects' => $projects,
            'report' => new LaporanDonor(),
        ]);
    }

    /**
     * Store a newly created donor report in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_proyek' => 'required|exists:proyek,kode_proyek',
            'periode' => 'required|string|max:50',
            'total_anggaran' => 'required|numeric',
            'total_realisasi' => 'required|numeric',
            'file_laporan' => 'required|file|mimes:pdf,zip,doc,docx|max:10240',
            'realisasi_kegiatan' => 'nullable|string',
            'realisasi_keuangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = 'submitted';

        if ($request->hasFile('file_laporan')) {
            $path = $request->file('file_laporan')->store('donor_reports', 'public');
            $data['file_laporan'] = $path;
        }

        LaporanDonor::create($data);

        return redirect()->route('reports.donor.index')
            ->with('success', 'Laporan donor berhasil diunggah.');
    }

    /**
     * Show report details.
     */
    public function show(LaporanDonor $report)
    {
        $report->load(['proyek', 'createdBy']);
        return view('reports.donor.show', [
            'report' => $report,
        ]);
    }

    /**
     * Download report file.
     */
    public function download(LaporanDonor $report)
    {
        if (!$report->file_laporan || !Storage::disk('public')->exists($report->file_laporan)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($report->file_laporan);
    }
}
