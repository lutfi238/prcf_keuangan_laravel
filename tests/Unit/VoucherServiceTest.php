<?php

namespace Tests\Unit;

use App\Services\VoucherService;
use PHPUnit\Framework\TestCase;

/**
 * Unit Tests untuk VoucherService
 * 
 * Test ini tidak memerlukan database karena menguji logic murni
 */
class VoucherServiceTest extends TestCase
{
    private VoucherService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VoucherService();
    }

    /**
     * Test format voucher number validation
     */
    public function test_valid_voucher_format(): void
    {
        // Format valid: YYYY/MM/PROJ/NNN
        $this->assertTrue($this->service->isValidFormat('2026/01/RC01/001'));
        $this->assertTrue($this->service->isValidFormat('2026/12/TEST-001/999'));
        $this->assertTrue($this->service->isValidFormat('2025/06/ABCD/123'));
    }

    public function test_invalid_voucher_format(): void
    {
        // Format tidak valid
        $this->assertFalse($this->service->isValidFormat('BB-BD-xxx-PROPxxx')); // Format lama
        $this->assertFalse($this->service->isValidFormat('2026-01-RC01-001')); // Wrong separator
        $this->assertFalse($this->service->isValidFormat('26/01/RC01/001')); // 2-digit year
        $this->assertFalse($this->service->isValidFormat('2026/1/RC01/001')); // 1-digit month
        $this->assertFalse($this->service->isValidFormat('2026/01/RC01/01')); // 2-digit sequence
        $this->assertFalse($this->service->isValidFormat('')); // Empty
        $this->assertFalse($this->service->isValidFormat('random-string')); // Random
    }

    /**
     * Test parsing voucher number
     */
    public function test_parse_valid_voucher(): void
    {
        $result = $this->service->parse('2026/01/RC01/001');
        
        $this->assertIsArray($result);
        $this->assertEquals('2026', $result['year']);
        $this->assertEquals('01', $result['month']);
        $this->assertEquals('RC01', $result['project_code']);
        $this->assertEquals(1, $result['sequence']);
    }

    public function test_parse_voucher_with_larger_sequence(): void
    {
        $result = $this->service->parse('2026/12/TEST-001/999');
        
        $this->assertIsArray($result);
        $this->assertEquals('2026', $result['year']);
        $this->assertEquals('12', $result['month']);
        $this->assertEquals('TEST-001', $result['project_code']);
        $this->assertEquals(999, $result['sequence']);
    }

    public function test_parse_invalid_voucher_returns_null(): void
    {
        $this->assertNull($this->service->parse('invalid-format'));
        $this->assertNull($this->service->parse(''));
        $this->assertNull($this->service->parse('BB-BD-xxx'));
    }

    /**
     * Test voucher format matches PHP native finance_functions.php
     */
    public function test_voucher_format_matches_php_native_standard(): void
    {
        // Format standar dari finance_functions.php:8-33: YYYY/MM/PROJ/001
        $validExamples = [
            '2026/01/RC01/001',
            '2026/02/RC01/002', 
            '2026/03/TEST-001/100',
        ];

        foreach ($validExamples as $voucher) {
            $this->assertTrue(
                $this->service->isValidFormat($voucher),
                "Voucher '$voucher' should be valid"
            );
        }
    }

    /**
     * Test bahwa format lama dari review_proposal_fm.php:68 tidak diterima
     */
    public function test_old_format_from_review_proposal_fm_is_invalid(): void
    {
        // Format lama: "BB-BD-xxx-PROP000001-01" 
        // Ini tidak valid di sistem baru (seharusnya menggunakan format standar)
        $oldFormat = 'BB-BD-20260107-123456-PROP000001-01';
        
        $this->assertFalse(
            $this->service->isValidFormat($oldFormat),
            "Old format from review_proposal_fm.php should not be valid"
        );
    }
}