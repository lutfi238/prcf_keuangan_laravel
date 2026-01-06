<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\BukuBankHeader;
use App\Models\BukuPiutangHeader;
use App\Models\BukuPiutangUnliquidated;
use App\Models\ProjectCodeBudget;
use App\Models\Proposal;
use App\Models\ProposalBudgetDetail;
use App\Models\Proyek;
use App\Models\User;
use App\Models\Village;
use App\Services\ProposalSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Parity Verification Tests
 * 
 * Membuktikan bahwa implementasi Laravel identik dengan PHP native.
 * Setiap test case mewakili satu gap yang sudah diperbaiki.
 */
class ParityVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pm;
    private User $fm;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create([
            'role' => 'Admin',
            'status' => 'active',
        ]);

        $this->pm = User::factory()->create([
            'role' => 'Project Manager',
            'status' => 'active',
        ]);

        $this->fm = User::factory()->create([
            'role' => 'Finance Manager',
            'status' => 'active',
        ]);
    }

    /**
     * GAP-001 TEST: Settlement creates BukuPiutangUnliquidated entries
     * 
     * PHP Native: review_proposal_fm.php:102-109 inserts into buku_piutang_unliquidated
     * Laravel: ProposalSettlementService now creates BukuPiutangUnliquidated entries
     */
    public function test_gap001_settlement_creates_unliquidated_entries(): void
    {
        // Arrange: Create project, village, budget, and proposal
        $village = Village::factory()->create(['village_abbr' => 'TST']);
        $project = Proyek::factory()->create(['kode_proyek' => 'TEST-001']);
        
        ProjectCodeBudget::create([
            'kode_proyek' => 'TEST-001',
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-TST-01',
            'budget_usd' => 10000,
            'budget_idr' => 150000000,
            'used_usd' => 0,
            'used_idr' => 0,
            'exrate' => 15000,
        ]);

        $proposal = Proposal::create([
            'judul_proposal' => 'Test Proposal GAP-001',
            'kode_proyek' => 'TEST-001',
            'pemohon' => $this->pm->nama,
            'pj' => $this->pm->nama,
            'date' => now()->format('Y-m-d'),
            'currency' => 'USD',
            'exrate_at_submission' => 15000,
            'status' => ProposalStatus::Submitted,
        ]);

        ProposalBudgetDetail::create([
            'id_proposal' => $proposal->id_proposal,
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-TST-01',
            'requested_usd' => 1000,
            'requested_idr' => 15000000,
            'exrate' => 15000,
            'description' => 'Test budget detail',
        ]);

        // Act: Process settlement
        $service = new ProposalSettlementService();
        $results = $service->processApproval($proposal);

        // Assert: Unliquidated entry is created
        $this->assertTrue($results['success']);
        $this->assertCount(1, $results['unliquidated_entries']);
        
        $unliquidated = BukuPiutangUnliquidated::where('name', $this->pm->nama)->first();
        $this->assertNotNull($unliquidated, 'Unliquidated entry should be created');
        $this->assertEquals('pending', $unliquidated->status);
        $this->assertEquals(15000000, $unliquidated->nilai_idr);
        $this->assertEquals(1000, $unliquidated->nilai_usd);
        $this->assertStringContainsString('Advance for:', $unliquidated->description);
    }

    /**
     * GAP-001 TEST: Voucher number format matches PHP native
     * 
     * PHP Native: finance_functions.php:8-33 generates YYYY/MM/PROJ/001
     * Laravel: ProposalSettlementService::generateVoucherNo() now matches
     */
    public function test_gap001_voucher_number_format(): void
    {
        // Similar setup as above
        $village = Village::factory()->create(['village_abbr' => 'TST']);
        $project = Proyek::factory()->create(['kode_proyek' => 'RC01']);
        
        ProjectCodeBudget::create([
            'kode_proyek' => 'RC01',
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-TST-01',
            'budget_usd' => 10000,
            'budget_idr' => 150000000,
            'used_usd' => 0,
            'used_idr' => 0,
            'exrate' => 15000,
        ]);

        $proposal = Proposal::create([
            'judul_proposal' => 'Test Voucher Format',
            'kode_proyek' => 'RC01',
            'pemohon' => $this->pm->nama,
            'pj' => $this->pm->nama,
            'date' => now()->format('Y-m-d'),
            'currency' => 'USD',
            'exrate_at_submission' => 15000,
            'status' => ProposalStatus::Submitted,
        ]);

        ProposalBudgetDetail::create([
            'id_proposal' => $proposal->id_proposal,
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-TST-01',
            'requested_usd' => 500,
            'requested_idr' => 7500000,
            'exrate' => 15000,
        ]);

        // Act
        $service = new ProposalSettlementService();
        $results = $service->processApproval($proposal);

        // Assert: Voucher format is YYYY/MM/PROJ/001
        $voucherNo = $results['receivable_entries'][0]['voucher_no'];
        $expectedPrefix = date('Y') . '/' . date('m') . '/RC01/';
        $this->assertStringStartsWith($expectedPrefix, $voucherNo);
        $this->assertMatchesRegularExpression('/^\d{4}\/\d{2}\/[A-Z0-9-]+\/\d{3}$/', $voucherNo);
    }

    /**
     * GAP-005 TEST: Admin cannot edit self
     * 
     * PHP Native: manage_users.php:62-65 blocks self-edit
     * Laravel: UserController::edit() and update() now block self-edit
     */
    public function test_gap005_admin_cannot_edit_self(): void
    {
        // Act: Admin tries to edit themselves
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $this->admin));

        // Assert: Redirected with error
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');
    }

    /**
     * GAP-005 TEST: Admin cannot update self
     */
    public function test_gap005_admin_cannot_update_self(): void
    {
        // Act: Admin tries to update themselves
        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $this->admin), [
                'nama' => 'New Name',
                'email' => $this->admin->email,
                'role' => 'Admin',
                'status' => 'active',
            ]);

        // Assert: Blocked with error
        $response->assertSessionHas('error');
    }

    /**
     * GAP-005 TEST: Admin can edit other users
     */
    public function test_gap005_admin_can_edit_other_users(): void
    {
        // Create another user to edit
        $otherUser = User::factory()->create([
            'role' => 'Project Manager',
            'status' => 'active',
        ]);

        // Act: Admin edits another user
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $otherUser));

        // Assert: Can access edit page
        $response->assertStatus(200);
    }

    /**
     * GAP-002 TEST: Export Excel generates correct format
     * 
     * PHP Native: export_bank_excel.php generates XML Excel
     * Laravel: BukuBankExportController::exportExcel() generates matching format
     */
    public function test_gap002_export_excel_format(): void
    {
        // Create test data
        $project = Proyek::factory()->create(['kode_proyek' => 'EXP-001']);
        
        $header = BukuBankHeader::create([
            'id_bank_header' => 'BH-TEST-001',
            'kode_proyek' => 'EXP-001',
            'periode_bulan' => '01',
            'periode_tahun' => '2026',
            'saldo_awal_idr' => 1000000,
            'saldo_awal_usd' => 100,
            'saldo_akhir_idr' => 1000000,
            'saldo_akhir_usd' => 100,
            'current_period_change_idr' => 0,
            'current_period_change_usd' => 0,
            'status_laporan' => 'draft',
            'tanggal_pembuatan' => now(),
        ]);

        // Act: Export Excel
        $response = $this->actingAs($this->fm)
            ->get(route('books.bank.export', ['id' => 'BH-TEST-001']));

        // Assert: Correct headers and content type
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        $this->assertStringContainsString('.xls', $response->headers->get('Content-Disposition'));
        
        // Assert: Content is XML Excel format
        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0"', $content);
        $this->assertStringContainsString('xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"', $content);
        $this->assertStringContainsString('BANK BOOK', $content);
    }

    /**
     * Verify settlement updates budget correctly
     * 
     * Both PHP and Laravel should increment used_usd and used_idr
     */
    public function test_settlement_updates_budget_correctly(): void
    {
        // Arrange
        $village = Village::factory()->create(['village_abbr' => 'BUD']);
        $project = Proyek::factory()->create(['kode_proyek' => 'BUD-001']);
        
        $budget = ProjectCodeBudget::create([
            'kode_proyek' => 'BUD-001',
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-BUD-01',
            'budget_usd' => 5000,
            'budget_idr' => 75000000,
            'used_usd' => 1000, // Already used 1000
            'used_idr' => 15000000,
            'exrate' => 15000,
        ]);

        $proposal = Proposal::create([
            'judul_proposal' => 'Test Budget Update',
            'kode_proyek' => 'BUD-001',
            'pemohon' => $this->pm->nama,
            'pj' => $this->pm->nama,
            'date' => now()->format('Y-m-d'),
            'currency' => 'USD',
            'exrate_at_submission' => 15000,
            'status' => ProposalStatus::Submitted,
        ]);

        ProposalBudgetDetail::create([
            'id_proposal' => $proposal->id_proposal,
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-BUD-01',
            'requested_usd' => 500,
            'requested_idr' => 7500000,
            'exrate' => 15000,
        ]);

        // Act
        $service = new ProposalSettlementService();
        $results = $service->processApproval($proposal);

        // Assert: Budget used increased
        $budget->refresh();
        $this->assertEquals(1500, $budget->used_usd); // 1000 + 500
        $this->assertEquals(22500000, $budget->used_idr); // 15000000 + 7500000
    }

    /**
     * Verify bank book entry is created correctly
     */
    public function test_settlement_creates_bank_book_entry(): void
    {
        // Arrange
        $village = Village::factory()->create(['village_abbr' => 'BNK']);
        $project = Proyek::factory()->create(['kode_proyek' => 'BNK-001']);
        
        ProjectCodeBudget::create([
            'kode_proyek' => 'BNK-001',
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-BNK-01',
            'budget_usd' => 5000,
            'budget_idr' => 75000000,
            'used_usd' => 0,
            'used_idr' => 0,
            'exrate' => 15000,
        ]);

        $proposal = Proposal::create([
            'judul_proposal' => 'Test Bank Entry',
            'kode_proyek' => 'BNK-001',
            'pemohon' => $this->pm->nama,
            'pj' => $this->pm->nama,
            'date' => now()->format('Y-m-d'),
            'currency' => 'USD',
            'exrate_at_submission' => 15000,
            'status' => ProposalStatus::Submitted,
        ]);

        ProposalBudgetDetail::create([
            'id_proposal' => $proposal->id_proposal,
            'id_village' => $village->id_village,
            'exp_code' => 'EXP01',
            'place_code' => 'EXP01-BNK-01',
            'requested_usd' => 500,
            'requested_idr' => 7500000,
            'exrate' => 15000,
        ]);

        // Act
        $service = new ProposalSettlementService();
        $results = $service->processApproval($proposal);

        // Assert: Bank book entry created with credit (money out)
        $this->assertCount(1, $results['bank_entries']);
        $bankEntry = $results['bank_entries'][0];
        $this->assertEquals(7500000, $bankEntry['credit_idr']);
        $this->assertEquals(500, $bankEntry['credit_usd']);
    }
}