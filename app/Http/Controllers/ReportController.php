<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\LaporanKeuanganHeader;
use App\Models\LaporanKeuanganDetail;
use App\Models\Notification;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of reports.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LaporanKeuanganHeader::with(['proyek', 'createdBy', 'verifiedBy', 'approvedBy']);

        // Role-based filtering
        if ($user->isProjectManager()) {
            $query->where('created_by', $user->id_user);
        } elseif ($user->isStaffAccountant()) {
            // SA sees reports for verification
            if ($request->filter !== 'all') {
                $query->whereIn('status_lap', [
                    ReportStatus::Submitted->value,
                    ReportStatus::Verified->value,
                ]);
            }
        } elseif ($user->isFinanceManager()) {
            // FM sees verified reports for approval
            if ($request->filter !== 'all') {
                $query->whereIn('status_lap', [
                    ReportStatus::Verified->value,
                    ReportStatus::Approved->value,
                ]);
            }
        } elseif ($user->isDirektur()) {
            // Direktur sees only approved reports (read-only)
            $query->where('status_lap', ReportStatus::Approved->value);
        }
        // Admin sees all

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_lap', $request->status);
        }

        // Filter by project
        if ($request->filled('project')) {
            $query->where('kode_projek', $request->project);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('pelaksana', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        $projects = Proyek::where('status_proyek', 'ongoing')->get();

        return view('reports.index', [
            'reports' => $reports,
            'projects' => $projects,
            'statuses' => ReportStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $projects = Proyek::where('status_proyek', 'ongoing')->get();

        return view('reports.form', [
            'report' => null,
            'projects' => $projects,
        ]);
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_projek' => 'required|exists:proyek,kode_proyek',
            'nama_kegiatan' => 'required|string|max:255',
            'pelaksana' => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'tanggal_laporan' => 'required|date',
            'mata_uang' => 'required|in:IDR,USD',
            'exrate' => 'required|numeric|min:1',
            'details' => 'required|array|min:1',
            'details.*.invoice_date' => 'required|date',
            'details.*.item_desc' => 'required|string',
            'details.*.recipient' => 'required|string',
            'details.*.unit_total' => 'required|integer|min:1',
            'details.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $project = Proyek::find($request->kode_projek);

            // Create header
            $report = LaporanKeuanganHeader::create([
                'kode_projek' => $request->kode_projek,
                'nama_projek' => $project->nama_proyek,
                'nama_kegiatan' => $request->nama_kegiatan,
                'pelaksana' => $request->pelaksana,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'tanggal_laporan' => $request->tanggal_laporan,
                'mata_uang' => $request->mata_uang,
                'exrate' => $request->exrate,
                'created_by' => Auth::id(),
                'status_lap' => ReportStatus::Draft,
            ]);

            // Create details
            foreach ($request->details as $index => $detail) {
                $filePath = null;
                if ($request->hasFile("details.{$index}.file_nota")) {
                    $filePath = $request->file("details.{$index}.file_nota")
                        ->store('uploads/receipts', 'public');
                }

                LaporanKeuanganDetail::create([
                    'id_laporan_keu' => $report->id_laporan_keu,
                    'invoice_no' => $detail['invoice_no'] ?? '',
                    'invoice_date' => $detail['invoice_date'],
                    'item_desc' => $detail['item_desc'],
                    'recipient' => $detail['recipient'],
                    'place_code' => $detail['place_code'] ?? '',
                    'exp_code' => $detail['exp_code'] ?? '',
                    'unit_total' => $detail['unit_total'],
                    'unit_cost' => $detail['unit_cost'],
                    'requested' => $detail['requested'] ?? 0,
                    'actual' => $detail['unit_total'] * $detail['unit_cost'],
                    'balance' => 0,
                    'explanation' => $detail['explanation'] ?? null,
                    'file_nota' => $filePath,
                ]);
            }

            DB::commit();

            return redirect()->route('reports.show', $report->id_laporan_keu)
                ->with('success', 'Laporan keuangan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Report creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified report.
     */
    public function show(LaporanKeuanganHeader $report)
    {
        $report->load(['proyek', 'createdBy', 'verifiedBy', 'approvedBy', 'details']);

        return view('reports.show', [
            'report' => $report,
        ]);
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(LaporanKeuanganHeader $report)
    {
        if (!in_array($report->status_lap, [ReportStatus::Draft, ReportStatus::RevisionRequested])) {
            return back()->with('error', 'Laporan tidak dapat diedit.');
        }

        $projects = Proyek::where('status_proyek', 'ongoing')->get();
        $report->load('details');

        return view('reports.form', [
            'report' => $report,
            'projects' => $projects,
        ]);
    }

    /**
     * Update the specified report.
     */
    public function update(Request $request, LaporanKeuanganHeader $report)
    {
        if (!in_array($report->status_lap, [ReportStatus::Draft, ReportStatus::RevisionRequested])) {
            return back()->with('error', 'Laporan tidak dapat diedit.');
        }

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'pelaksana' => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'tanggal_laporan' => 'required|date',
            'mata_uang' => 'required|in:IDR,USD',
            'exrate' => 'required|numeric|min:1',
        ]);

        $report->update($request->only([
            'nama_kegiatan', 'pelaksana', 'tanggal_pelaksanaan',
            'tanggal_laporan', 'mata_uang', 'exrate'
        ]));

        return redirect()->route('reports.show', $report->id_laporan_keu)
            ->with('success', 'Laporan berhasil diupdate.');
    }

    /**
     * Submit report for verification.
     */
    public function submit(LaporanKeuanganHeader $report)
    {
        if (!in_array($report->status_lap, [ReportStatus::Draft, ReportStatus::RevisionRequested])) {
            return back()->with('error', 'Hanya laporan draft yang dapat disubmit.');
        }

        $report->update(['status_lap' => ReportStatus::Submitted]);

        // Notify Staff Accountants
        $sas = User::where('role', 'Staff Accountant')->where('status', 'active')->get();
        foreach ($sas as $sa) {
            Notification::create([
                'user_id' => $sa->id_user,
                'type' => 'report_submitted',
                'message' => "Laporan keuangan '{$report->nama_kegiatan}' memerlukan verifikasi Anda.",
                'link' => route('reports.show', $report->id_laporan_keu),
                'related_id' => $report->id_laporan_keu,
                'related_type' => 'report',
            ]);
        }

        return back()->with('success', 'Laporan berhasil disubmit untuk verifikasi.');
    }

    /**
     * Remove the specified report.
     */
    public function destroy(LaporanKeuanganHeader $report)
    {
        if ($report->status_lap !== ReportStatus::Draft) {
            return back()->with('error', 'Laporan tidak dapat dihapus setelah disubmit.');
        }

        // Delete receipt files
        foreach ($report->details as $detail) {
            if ($detail->file_nota) {
                Storage::disk('public')->delete($detail->file_nota);
            }
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
