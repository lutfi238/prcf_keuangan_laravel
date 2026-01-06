<?php

namespace App\Http\Controllers;

use App\Models\BukuBankHeader;
use App\Models\BukuBankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * GAP-002 FIX: Export Excel Buku Bank
 * Implementasi sesuai dengan PHP native export_bank_excel.php
 */
class BukuBankExportController extends Controller
{
    /**
     * Export buku bank to Excel (XML Spreadsheet format)
     * Sesuai dengan PHP native pages/books/export_bank_excel.php
     */
    public function exportExcel(string $id)
    {
        // Get header data
        $header = BukuBankHeader::with('proyek')
            ->where('id_bank_header', $id)
            ->firstOrFail();

        // Get detail transactions
        $details = BukuBankDetail::where('id_bank_header', $id)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id_detail_bank', 'asc')
            ->get();

        // Month names
        $monthNames = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        $monthName = $monthNames[$header->periode_bulan] ?? 'Unknown';
        $year = $header->periode_tahun;
        $primaryCurrency = $header->currency ?? 'IDR';

        // Calculate totals
        $totalDebitIdr = $details->sum('debit_idr');
        $totalCreditIdr = $details->sum('credit_idr');
        $totalDebitUsd = $details->sum('debit_usd');
        $totalCreditUsd = $details->sum('credit_usd');

        // Create filename
        $filename = sprintf(
            "Bank_Book_%s_%s_%s_%s_%s.xls",
            $primaryCurrency,
            $header->kode_proyek,
            $year,
            $monthName,
            date('YmdHis')
        );

        // Generate Excel XML content
        $content = $this->generateExcelXml($header, $details, $monthName, $year, $primaryCurrency, $totalDebitIdr, $totalCreditIdr, $totalDebitUsd, $totalCreditUsd);

        return Response::make($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Generate Excel XML content sesuai format PHP native
     */
    private function generateExcelXml($header, $details, $monthName, $year, $primaryCurrency, $totalDebitIdr, $totalCreditIdr, $totalDebitUsd, $totalCreditUsd): string
    {
        $projectName = $header->proyek->nama_proyek ?? '';
        $preparedBy = $header->prepared_by ?? '';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<?mso-application progid="Excel.Sheet"?>';
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">';

        // Document Properties
        $xml .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>PRCF Keuangan Dashboard</Author>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
 </DocumentProperties>';

        // Styles
        $xml .= $this->getExcelStyles();

        // Worksheet
        $xml .= '<Worksheet ss:Name="Bank Book">
  <Table>';

        // Column widths
        $xml .= '<Column ss:Index="1" ss:Width="130"/>
   <Column ss:Width="80"/>
   <Column ss:Width="100"/>
   <Column ss:Width="200"/>
   <Column ss:Width="150"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>';

        // Title Row
        $xml .= '<Row ss:Height="25">
    <Cell ss:MergeAcross="9" ss:StyleID="s62">
     <Data ss:Type="String">BANK BOOK - ' . strtoupper($primaryCurrency) . '</Data>
    </Cell>
   </Row>';

        // Project Info
        $xml .= $this->getProjectInfoRows($header, $projectName, $monthName, $year);

        // Empty Row
        $xml .= '<Row ss:Height="15"></Row>';

        // Table Header
        $xml .= '<Row ss:Height="30">
    <Cell ss:StyleID="s64"><Data ss:Type="String">Date</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Reference</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Title Activity</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Cost Description</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Recipient</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Debit (IDR)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Credit (IDR)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Balance (IDR)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Debit (USD)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Credit (USD)</Data></Cell>
   </Row>';

        // Beginning Balance Row
        $xml .= '<Row ss:Height="20">
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s69">
     <Data ss:Type="String">Beginning Balance</Data>
    </Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">0</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">0</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $header->saldo_awal_idr . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">0</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">0</Data></Cell>
   </Row>';

        // Transaction rows
        foreach ($details as $detail) {
            $xml .= '<Row ss:Height="20">
    <Cell ss:StyleID="s67">
     <Data ss:Type="String">' . date('d/m/Y', strtotime($detail->tanggal)) . '</Data>
    </Cell>
    <Cell ss:StyleID="s65">
     <Data ss:Type="String">' . htmlspecialchars($detail->reff ?? '') . '</Data>
    </Cell>
    <Cell ss:StyleID="s65">
     <Data ss:Type="String">' . htmlspecialchars($detail->title_activity ?? '') . '</Data>
    </Cell>
    <Cell ss:StyleID="s65">
     <Data ss:Type="String">' . htmlspecialchars($detail->cost_description ?? '') . '</Data>
    </Cell>
    <Cell ss:StyleID="s65">
     <Data ss:Type="String">' . htmlspecialchars($detail->recipient ?? '') . '</Data>
    </Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . ($detail->debit_idr ?? 0) . '</Data>
    </Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . ($detail->credit_idr ?? 0) . '</Data>
    </Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . ($detail->balance_idr ?? 0) . '</Data>
    </Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . ($detail->debit_usd ?? 0) . '</Data>
    </Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . ($detail->credit_usd ?? 0) . '</Data>
    </Cell>
   </Row>';
        }

        // Ending Balance Row
        $xml .= '<Row ss:Height="22">
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s68">
     <Data ss:Type="String">Ending Balance</Data>
    </Cell>
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s68">
     <Data ss:Type="Number">' . $header->saldo_akhir_idr . '</Data>
    </Cell>
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>
   </Row>';

        // Summary Section
        $xml .= '<Row ss:Height="15"></Row>
   <Row ss:Height="15"></Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Total Debit (IDR):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $totalDebitIdr . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Total Credit (IDR):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $totalCreditIdr . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Net Change (IDR):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $header->current_period_change_idr . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="15"></Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Total Debit (USD):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $totalDebitUsd . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Total Credit (USD):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $totalCreditUsd . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Net Change (USD):</Data></Cell>
    <Cell ss:StyleID="s66">
     <Data ss:Type="Number">' . $header->current_period_change_usd . '</Data>
    </Cell>
   </Row>';

        // Footer
        $xml .= '<Row ss:Height="15"></Row>
   <Row ss:Height="15"></Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="2">
     <Data ss:Type="String">Prepared by: ' . htmlspecialchars($preparedBy) . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="2">
     <Data ss:Type="String">Generated on: ' . date('d/m/Y H:i:s') . '</Data>
    </Cell>
   </Row>';

        $xml .= '</Table>';

        // Worksheet Options
        $xml .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Layout x:Orientation="Landscape"/>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <FitToPage/>
   <Print>
    <FitWidth>1</FitWidth>
    <FitHeight>0</FitHeight>
    <ValidPrinterInfo/>
    <PaperSizeIndex>1</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>0</ActiveRow>
     <ActiveCol>0</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>';

        return $xml;
    }

    /**
     * Get Excel styles - sesuai dengan PHP native
     */
    private function getExcelStyles(): string
    {
        return '<Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" ss:Size="11"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s62">
   <Font ss:FontName="Calibri" ss:Size="16" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s63">
   <Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s64">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s65">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="s66">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="s67">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="s68">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0.00"/>
   <Interior ss:Color="#E7E6E6" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s69">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s70">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
  </Style>
 </Styles>';
    }

    /**
     * Get project info rows
     */
    private function getProjectInfoRows($header, $projectName, $monthName, $year): string
    {
        return '<Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Project Code:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . htmlspecialchars($header->kode_proyek) . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Project Name:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . htmlspecialchars($projectName) . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Period:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . $monthName . ' ' . $year . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Bank Name:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . htmlspecialchars($header->bank_name ?? '') . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Account Name:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . htmlspecialchars($header->account_name ?? '') . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Account Number:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="String">' . htmlspecialchars($header->account_number ?? '') . '</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="s69"><Data ss:Type="String">Exchange Rate:</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="s70">
     <Data ss:Type="Number">' . ($header->exrate ?? 0) . '</Data>
    </Cell>
   </Row>';
    }
}