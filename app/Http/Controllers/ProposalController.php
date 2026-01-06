<?php

namespace App\Http\Controllers;

use App\Enums\ProposalStatus;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProposalBudgetDetail;
use App\Models\ProjectCodeBudget;
use App\Models\Proyek;
use App\Models\Village;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    /**
     * Display a listing of proposals.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Proposal::with(['proyek', 'approvedByFm']);

        // Role-based filtering
        if ($user->isProjectManager()) {
            // PM sees only their own proposals
            $query->where('pemohon', $user->nama);
        } elseif ($user->isFinanceManager()) {
            // FM sees proposals waiting for their approval
            if ($request->filter === 'mine') {
                $query->where('approved_by_fm', $user->id_user);
            } else {
                $query->whereIn('status', [
                    ProposalStatus::Submitted->value,
                    ProposalStatus::ApprovedFM->value,
                    ProposalStatus::Approved->value,
                    ProposalStatus::Rejected->value,
                ]);
            }
        } elseif ($user->isDirektur()) {
            // Direktur sees only approved proposals (read-only)
            $query->where('status', ProposalStatus::ApprovedFM->value);
        } elseif ($user->isStaffAccountant()) {
            // SA sees approved proposals for reference
            $query->whereIn('status', [
                ProposalStatus::ApprovedFM->value,
                ProposalStatus::Approved->value,
            ]);
        }
        // Admin sees all

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by project
        if ($request->filled('project')) {
            $query->where('kode_proyek', $request->project);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_proposal', 'like', "%{$search}%")
                  ->orWhere('pemohon', 'like', "%{$search}%")
                  ->orWhere('pj', 'like', "%{$search}%");
            });
        }

        $proposals = $query->orderBy('created_at', 'desc')->paginate(15);
        $projects = Proyek::where('status_proyek', 'ongoing')->get();

        return view('proposals.index', [
            'proposals' => $proposals,
            'projects' => $projects,
            'statuses' => ProposalStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new proposal.
     */
    public function create()
    {
        $projects = Proyek::where('status_proyek', 'ongoing')->get();
        $villages = Village::orderBy('village_name')->get();

        return view('proposals.form', [
            'proposal' => null,
            'projects' => $projects,
            'villages' => $villages,
        ]);
    }

    /**
     * Store a newly created proposal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_proposal' => 'required|string|max:255',
            'pj' => 'required|string|max:255',
            'kode_proyek' => 'required|exists:proyek,kode_proyek',
            'date' => 'required|date',
            'currency' => 'required|in:USD,IDR',
            'exrate_at_submission' => 'required|numeric|min:1',
            'tor' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'file_budget' => 'nullable|file|mimes:pdf,xls,xlsx|max:10240',
            'budget_details' => 'required|array|min:1',
            'budget_details.*.id_village' => 'required|exists:villages,id_village',
            'budget_details.*.exp_code' => 'required|string',
            'budget_details.*.requested_usd' => 'required|numeric|min:0',
            'budget_details.*.description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle file uploads
            $torPath = null;
            $budgetPath = null;

            if ($request->hasFile('tor')) {
                $torPath = $request->file('tor')->store('uploads/tor', 'public');
            }

            if ($request->hasFile('file_budget')) {
                $budgetPath = $request->file('file_budget')->store('uploads/budgets', 'public');
            }

            // Calculate totals
            $totalUsd = 0;
            $totalIdr = 0;
            $exrate = $request->exrate_at_submission;

            foreach ($request->budget_details as $detail) {
                $totalUsd += $detail['requested_usd'];
            }
            $totalIdr = $totalUsd * $exrate;

            // Create proposal
            $proposal = Proposal::create([
                'judul_proposal' => $request->judul_proposal,
                'pj' => $request->pj,
                'date' => $request->date,
                'pemohon' => Auth::user()->nama,
                'status' => ProposalStatus::Draft,
                'kode_proyek' => $request->kode_proyek,
                'tor' => $torPath,
                'file_budget' => $budgetPath,
                'total_budget_usd' => $totalUsd,
                'total_budget_idr' => $totalIdr,
                'currency' => $request->currency,
                'exrate_at_submission' => $exrate,
            ]);

            // Create budget details
            foreach ($request->budget_details as $detail) {
                // Get place_code from project_code_budgets
                $budget = ProjectCodeBudget::where('kode_proyek', $request->kode_proyek)
                    ->where('id_village', $detail['id_village'])
                    ->where('exp_code', $detail['exp_code'])
                    ->first();

                ProposalBudgetDetail::create([
                    'id_proposal' => $proposal->id_proposal,
                    'id_village' => $detail['id_village'],
                    'exp_code' => $detail['exp_code'],
                    'place_code' => $budget?->place_code ?? '',
                    'requested_usd' => $detail['requested_usd'],
                    'requested_idr' => $detail['requested_usd'] * $exrate,
                    'currency' => $request->currency,
                    'exrate' => $exrate,
                    'description' => $detail['description'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('proposals.show', $proposal->id_proposal)
                ->with('success', 'Proposal berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Proposal creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified proposal.
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['proyek', 'approvedByFm', 'budgetDetails.village']);

        return view('proposals.show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Show the form for editing the specified proposal.
     */
    public function edit(Proposal $proposal)
    {
        // Only allow editing draft proposals
        if (!$proposal->isEditable()) {
            return back()->with('error', 'Proposal tidak dapat diedit setelah disubmit.');
        }

        $projects = Proyek::where('status_proyek', 'ongoing')->get();
        $villages = Village::orderBy('village_name')->get();
        $proposal->load('budgetDetails');

        return view('proposals.form', [
            'proposal' => $proposal,
            'projects' => $projects,
            'villages' => $villages,
        ]);
    }

    /**
     * Update the specified proposal.
     */
    public function update(Request $request, Proposal $proposal)
    {
        if (!$proposal->isEditable()) {
            return back()->with('error', 'Proposal tidak dapat diedit setelah disubmit.');
        }

        $request->validate([
            'judul_proposal' => 'required|string|max:255',
            'pj' => 'required|string|max:255',
            'kode_proyek' => 'required|exists:proyek,kode_proyek',
            'date' => 'required|date',
            'currency' => 'required|in:USD,IDR',
            'exrate_at_submission' => 'required|numeric|min:1',
            'tor' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'file_budget' => 'nullable|file|mimes:pdf,xls,xlsx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Handle file uploads
            if ($request->hasFile('tor')) {
                if ($proposal->tor) {
                    Storage::disk('public')->delete($proposal->tor);
                }
                $proposal->tor = $request->file('tor')->store('uploads/tor', 'public');
            }

            if ($request->hasFile('file_budget')) {
                if ($proposal->file_budget) {
                    Storage::disk('public')->delete($proposal->file_budget);
                }
                $proposal->file_budget = $request->file('file_budget')->store('uploads/budgets', 'public');
            }

            $proposal->update([
                'judul_proposal' => $request->judul_proposal,
                'pj' => $request->pj,
                'date' => $request->date,
                'kode_proyek' => $request->kode_proyek,
                'currency' => $request->currency,
                'exrate_at_submission' => $request->exrate_at_submission,
            ]);

            DB::commit();

            return redirect()->route('proposals.show', $proposal->id_proposal)
                ->with('success', 'Proposal berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update proposal: ' . $e->getMessage());
        }
    }

    /**
     * Submit proposal for approval.
     */
    public function submit(Proposal $proposal)
    {
        if ($proposal->status !== ProposalStatus::Draft) {
            return back()->with('error', 'Hanya proposal draft yang dapat disubmit.');
        }

        $proposal->update(['status' => ProposalStatus::Submitted]);

        // Notify Finance Managers
        $fms = User::where('role', 'Finance Manager')->where('status', 'active')->get();
        foreach ($fms as $fm) {
            Notification::proposalSubmitted($proposal, $fm);
        }

        return back()->with('success', 'Proposal berhasil disubmit untuk review.');
    }

    /**
     * Remove the specified proposal.
     */
    public function destroy(Proposal $proposal)
    {
        if (!$proposal->isEditable()) {
            return back()->with('error', 'Proposal tidak dapat dihapus setelah disubmit.');
        }

        // Delete files
        if ($proposal->tor) {
            Storage::disk('public')->delete($proposal->tor);
        }
        if ($proposal->file_budget) {
            Storage::disk('public')->delete($proposal->file_budget);
        }

        $proposal->delete();

        return redirect()->route('proposals.index')
            ->with('success', 'Proposal berhasil dihapus.');
    }
}
