<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\LaporanKeuanganHeader;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportApprovalController extends Controller
{
    /**
     * Show SA verification page
     */
    public function verifySa(LaporanKeuanganHeader $report)
    {
        if ($report->status_lap !== ReportStatus::Submitted) {
            return back()->with('error', 'Laporan tidak dalam status menunggu verifikasi.');
        }

        $report->load(['proyek', 'createdBy', 'details']);

        return view('reports.verify-sa', [
            'report' => $report,
        ]);
    }

    /**
     * Verify report by Staff Accountant
     */
    public function verifySaSubmit(Request $request, LaporanKeuanganHeader $report)
    {
        $user = Auth::user();

        if (!$user->isStaffAccountant()) {
            return back()->with('error', 'Hanya Staff Accountant yang dapat memverifikasi laporan.');
        }

        if ($report->status_lap !== ReportStatus::Submitted) {
            return back()->with('error', 'Laporan tidak dalam status yang dapat diverifikasi.');
        }

        $request->validate([
            'status' => 'required|in:verified,revision',
            'notes' => 'required|string|max:1000',
        ]);

        if ($request->status === 'revision') {
            return $this->requestRevision($request, $report);
        }

        $report->update([
            'status_lap' => ReportStatus::Verified,
            'verified_by' => $user->id_user,
            'notes_sa' => $request->notes,
        ]);

        // Notify Finance Managers
        $fms = User::where('role', 'Finance Manager')->where('status', 'active')->get();
        foreach ($fms as $fm) {
            Notification::reportVerified($report, $fm);
        }

        // Notify PM
        if ($report->createdBy) {
            Notification::create([
                'user_id' => $report->created_by,
                'type' => 'report_verified',
                'message' => "Laporan '{$report->nama_kegiatan}' telah diverifikasi oleh Staff Accountant.",
                'link' => route('reports.show', $report->id_laporan_keu),
                'related_id' => $report->id_laporan_keu,
                'related_type' => 'report',
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil diverifikasi.');
    }

    /**
     * Show FM approval page
     */
    public function approveFm(LaporanKeuanganHeader $report)
    {
        if ($report->status_lap !== ReportStatus::Verified) {
            return back()->with('error', 'Laporan tidak dalam status menunggu approval FM.');
        }

        $report->load(['proyek', 'createdBy', 'verifiedBy', 'details']);

        return view('reports.approve-fm', [
            'report' => $report,
        ]);
    }

    /**
     * Approve report by Finance Manager
     */
    public function approveFmSubmit(Request $request, LaporanKeuanganHeader $report)
    {
        $user = Auth::user();

        if (!$user->isFinanceManager()) {
            return back()->with('error', 'Hanya Finance Manager yang dapat menyetujui laporan.');
        }

        if ($report->status_lap !== ReportStatus::Verified) {
            return back()->with('error', 'Laporan tidak dalam status yang dapat disetujui.');
        }

        $request->validate([
            'status' => 'required|in:approved,revision',
            'notes' => 'required|string|max:1000',
        ]);

        if ($request->status === 'revision') {
            return $this->requestRevision($request, $report);
        }

        $report->update([
            'status_lap' => ReportStatus::Approved,
            'approved_by' => $user->id_user,
            'notes_fm' => $request->notes,
        ]);

        // Notify PM
        if ($report->createdBy) {
            Notification::create([
                'user_id' => $report->created_by,
                'type' => 'report_approved',
                'message' => "Laporan '{$report->nama_kegiatan}' telah disetujui oleh Finance Manager.",
                'link' => route('reports.show', $report->id_laporan_keu),
                'related_id' => $report->id_laporan_keu,
                'related_type' => 'report',
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil disetujui.');
    }

    /**
     * Reject report
     */
    public function reject(Request $request, LaporanKeuanganHeader $report)
    {
        $user = Auth::user();

        if (!$user->isStaffAccountant() && !$user->isFinanceManager()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak laporan.');
        }

        $request->validate([
            'catatan_finance' => 'required|string|max:1000',
        ]);

        $report->update([
            'status_lap' => ReportStatus::Rejected,
            'notes_fm' => $request->catatan_finance,
        ]);

        // Notify PM
        if ($report->createdBy) {
            Notification::create([
                'user_id' => $report->created_by,
                'type' => 'report_rejected',
                'message' => "Laporan '{$report->nama_kegiatan}' ditolak: {$request->catatan_finance}",
                'link' => route('reports.show', $report->id_laporan_keu),
                'related_id' => $report->id_laporan_keu,
                'related_type' => 'report',
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil ditolak.');
    }

    /**
     * Request revision
     */
    public function requestRevision(Request $request, LaporanKeuanganHeader $report)
    {
        $user = Auth::user();

        if (!$user->isStaffAccountant() && !$user->isFinanceManager()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk meminta revisi.');
        }

        $request->validate([
            'catatan_finance' => 'required|string|max:1000',
        ]);

        $report->update([
            'status_lap' => ReportStatus::RevisionRequested,
            'notes_fm' => $request->notes ?? $request->catatan_finance,
        ]);

        // Notify PM
        if ($report->createdBy) {
            Notification::create([
                'user_id' => $report->created_by,
                'type' => 'report_revision',
                'message' => "Laporan '{$report->nama_kegiatan}' memerlukan revisi: {$request->catatan_finance}",
                'link' => route('reports.show', $report->id_laporan_keu),
                'related_id' => $report->id_laporan_keu,
                'related_type' => 'report',
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Permintaan revisi berhasil dikirim.');
    }
}
