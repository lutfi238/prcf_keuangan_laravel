<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProjectCodeBudget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalApprovalController extends Controller
{
    /**
     * Show FM review page with budget availability info
     */
    public function reviewFm(Proposal $proposal)
    {
        if ($proposal->status !== ProposalStatus::Submitted) {
            return back()->with('error', 'Proposal ini tidak dalam status menunggu review FM.');
        }

        $proposal->load(['proyek', 'budgetDetails.village']);

        // Calculate budget availability for each detail
        $totalRequested = 0;
        $totalAvailable = 0;
        $allSufficient = true;

        foreach ($proposal->budgetDetails as $detail) {
            // Find matching budget allocation
            $budget = ProjectCodeBudget::where('kode_proyek', $proposal->kode_proyek)
                ->where('exp_code', $detail->exp_code)
                ->where('id_village', $detail->id_village)
                ->first();

            if ($budget) {
                $detail->available_usd = $budget->remaining_usd;
                $detail->available_idr = $budget->remaining_idr;
                $detail->is_sufficient = $budget->hasSufficientBudget(
                    $detail->requested_usd ?? 0, 
                    $detail->requested_idr ?? 0
                );
                $detail->budget_exists = true;
            } else {
                $detail->available_usd = 0;
                $detail->available_idr = 0;
                $detail->is_sufficient = false;
                $detail->budget_exists = false;
            }

            $totalRequested += $detail->requested_usd ?? 0;
            $totalAvailable += $detail->available_usd;
            
            if (!$detail->is_sufficient) {
                $allSufficient = false;
            }
        }

        return view('proposals.review-fm', [
            'proposal' => $proposal,
            'totalRequested' => $totalRequested,
            'totalAvailable' => $totalAvailable,
            'allSufficient' => $allSufficient,
        ]);
    }

    /**
     * Approve proposal by Finance Manager (Stage 1)
     * Also processes settlement: Bank Book, Receivable, Budget deduction
     */
    public function approveFm(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        if (!$user->isFinanceManager()) {
            return back()->with('error', 'Hanya Finance Manager yang dapat menyetujui proposal.');
        }

        if ($proposal->status !== ProposalStatus::Submitted) {
            return back()->with('error', 'Proposal tidak dalam status yang dapat disetujui.');
        }

        $request->validate([
            'catatan_fm' => 'nullable|string|max:1000',
        ]);

        // Update proposal status
        $proposal->update([
            'status' => ProposalStatus::ApprovedFM,
            'approved_by_fm' => $user->id_user,
            'fm_approval_date' => now(),
        ]);

        // Process settlement: Bank Book, Receivable, Budget deduction
        $settlementService = new \App\Services\ProposalSettlementService();
        $settlementResult = $settlementService->processApproval($proposal);

        // Notify PM
        $pm = User::where('nama', $proposal->pemohon)->first();
        if ($pm) {
            Notification::proposalApproved($proposal, $pm, 'fm');
        }

        // Build success message with settlement details
        $message = 'Proposal berhasil disetujui.';
        if ($settlementResult['success']) {
            $bankCount = count($settlementResult['bank_entries']);
            $budgetCount = count($settlementResult['budget_updates']);
            $message .= " Settlement: {$bankCount} transaksi bank, {$budgetCount} budget diupdate.";
        } else {
            $message .= ' Warning: Settlement gagal - ' . implode(', ', $settlementResult['errors']);
        }

        return redirect()->route('proposals.index')
            ->with($settlementResult['success'] ? 'success' : 'warning', $message);
    }

    /**
     * Reject proposal
     */
    public function reject(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        if (!$user->isFinanceManager() && !$user->isDirektur()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak proposal.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'status' => ProposalStatus::Rejected,
        ]);

        // Notify PM
        $pm = User::where('nama', $proposal->pemohon)->first();
        if ($pm) {
            Notification::proposalRejected($proposal, $pm);
        }

        return redirect()->route('proposals.index')
            ->with('success', 'Proposal berhasil ditolak.');
    }
}
