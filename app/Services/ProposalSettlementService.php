<?php

namespace App\Services;

use App\Models\BukuBankDetail;
use App\Models\BukuBankHeader;
use App\Models\BukuPiutangDetail;
use App\Models\BukuPiutangHeader;
use App\Models\ProjectCodeBudget;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProposalSettlementService
{
    /**
     * Process proposal settlement when FM approves
     * 
     * This will:
     * 1. Deduct budget from ProjectCodeBudget (used_usd/used_idr)
     * 2. Create Bank Book entry (money out = credit)
     * 3. Create Receivable entry (PM owes = debit)
     */
    public function processApproval(Proposal $proposal): array
    {
        $results = [
            'success' => true,
            'budget_updates' => [],
            'bank_entries' => [],
            'receivable_entries' => [],
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            $proposal->load('budgetDetails.village');
            $today = now()->format('Y-m-d');

            // Get or create bank book header for this project and period
            $bankHeader = BukuBankHeader::getOrCreate($proposal->kode_proyek, $today);
            
            // Get or create receivable header for this project and period
            $receivableHeader = BukuPiutangHeader::getOrCreate($proposal->kode_proyek, $today);

            foreach ($proposal->budgetDetails as $detail) {
                $amountUsd = $detail->requested_usd ?? 0;
                $amountIdr = $detail->requested_idr ?? 0;
                $exrate = $detail->exrate ?? 15000;

                // 1. Update budget used amount
                $budget = ProjectCodeBudget::where('kode_proyek', $proposal->kode_proyek)
                    ->where('exp_code', $detail->exp_code)
                    ->where('id_village', $detail->id_village)
                    ->first();

                if ($budget) {
                    // Update used amounts (even if over budget, per requirement)
                    $budget->used_usd += $amountUsd;
                    $budget->used_idr += $amountIdr;
                    $budget->save();

                    $results['budget_updates'][] = [
                        'exp_code' => $detail->exp_code,
                        'place_code' => $budget->place_code,
                        'added_usd' => $amountUsd,
                        'new_used_usd' => $budget->used_usd,
                    ];
                }

                // 2. Create Bank Book detail entry (money out = credit)
                $bankDetail = BukuBankDetail::create([
                    'id_detail_bank' => BukuBankDetail::generateId(),
                    'id_bank_header' => $bankHeader->id_bank_header,
                    'tanggal' => $today,
                    'reff' => 'PRO-' . $proposal->id_proposal,
                    'title_activity' => $proposal->judul_proposal,
                    'cost_description' => 'Pencairan proposal: ' . $proposal->judul_proposal,
                    'recipient' => $proposal->pemohon,
                    'place_code' => $detail->place_code,
                    'exp_code' => $detail->exp_code,
                    'exrate' => $exrate,
                    'cost_curr' => 'IDR',
                    'debit_idr' => 0,
                    'debit_usd' => 0,
                    'credit_idr' => $amountIdr,
                    'credit_usd' => $amountUsd,
                    'status' => 'approved',
                ]);

                // Update bank header balance (credit = money out)
                $bankHeader->updateBalance($amountIdr, $amountUsd, true);

                $results['bank_entries'][] = [
                    'id' => $bankDetail->id_detail_bank,
                    'credit_idr' => $amountIdr,
                    'credit_usd' => $amountUsd,
                ];

                // 3. Create Receivable detail entry (PM owes = debit)
                $receivableDetail = BukuPiutangDetail::create([
                    'id_piutang' => $receivableHeader->id_piutang,
                    'tgl_trx' => $today,
                    'reff' => 'PRO-' . $proposal->id_proposal,
                    'description' => 'Piutang pencairan: ' . $proposal->judul_proposal,
                    'recipient' => $proposal->pemohon,
                    'p_code' => $detail->place_code,
                    'exp_code' => $detail->exp_code,
                    'exrate' => $exrate,
                    'debit_idr' => $amountIdr,
                    'debit_usd' => $amountUsd,
                    'credit_idr' => 0,
                    'credit_usd' => 0,
                ]);

                // Update receivable header balance (debit = increase receivable)
                $receivableHeader->updateBalance($amountIdr, $amountUsd, true);

                $results['receivable_entries'][] = [
                    'id' => $receivableDetail->id_detail_piutang,
                    'debit_idr' => $amountIdr,
                    'debit_usd' => $amountUsd,
                ];
            }

            DB::commit();

            Log::info('Proposal settlement completed', [
                'proposal_id' => $proposal->id_proposal,
                'budget_updates' => count($results['budget_updates']),
                'bank_entries' => count($results['bank_entries']),
                'receivable_entries' => count($results['receivable_entries']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $results['success'] = false;
            $results['errors'][] = $e->getMessage();

            Log::error('Proposal settlement failed', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * Reverse settlement (for cancellation or rejection after approval)
     */
    public function reverseSettlement(Proposal $proposal): array
    {
        $results = [
            'success' => true,
            'reversed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            $proposal->load('budgetDetails');

            foreach ($proposal->budgetDetails as $detail) {
                $amountUsd = $detail->requested_usd ?? 0;
                $amountIdr = $detail->requested_idr ?? 0;

                // Release budget
                $budget = ProjectCodeBudget::where('kode_proyek', $proposal->kode_proyek)
                    ->where('exp_code', $detail->exp_code)
                    ->where('id_village', $detail->id_village)
                    ->first();

                if ($budget) {
                    $budget->releaseBudget($amountUsd, $amountIdr);
                    $results['reversed']++;
                }
            }

            // Note: Bank and Receivable entries should be reversed via separate adjustment entries
            // rather than deleting records for audit trail purposes

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }
}
