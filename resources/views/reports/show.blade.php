@extends('layouts.app')

@section('title', 'Detail Laporan Keuangan')
@section('page-title', 'Detail Laporan')

@section('content')
<div class="space-y-6">
    <!-- Back & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
        <div class="flex flex-wrap gap-2">
            @if($report->status_lap->value === 'draft' && (auth()->id() === $report->created_by || auth()->user()->isAdmin()))
                <a href="{{ route('reports.edit', $report->id_laporan_keu) }}" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('reports.submit', $report->id_laporan_keu) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-1"></i> Submit
                    </button>
                </form>
                <form action="{{ route('reports.destroy', $report->id_laporan_keu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus laporan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            @endif

            @if($report->status_lap->value === 'submitted' && auth()->user()->isStaffAccountant())
                <a href="{{ route('reports.verify-sa', $report->id_laporan_keu) }}" class="px-4 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 font-semibold shadow">
                    <i class="fas fa-check-double mr-1"></i> Verifikasi Laporan
                </a>
            @endif

            @if($report->status_lap->value === 'verified' && auth()->user()->isFinanceManager())
                <a href="{{ route('reports.approve-fm', $report->id_laporan_keu) }}" class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700 font-semibold shadow">
                    <i class="fas fa-check-circle mr-1"></i> Approve Laporan (FM)
                </a>
            @endif
        </div>
    </div>

    <!-- Header Summary -->
    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $report->nama_kegiatan }}</h2>
                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                    <span><i class="fas fa-project-diagram mr-1"></i> {{ $report->kode_projek }} - {{ $report->nama_projek }}</span>
                    <span><i class="fas fa-user-edit mr-1"></i> Pelaksana: {{ $report->pelaksana }}</span>
                    <span><i class="fas fa-calendar-day mr-1"></i> Tgl: {{ $report->tanggal_pelaksanaan->format('d M Y') }}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm rounded-full {{ $report->status_lap->badgeColor() }}">
                    {{ $report->status_lap->label() }}
                </span>
                <p class="mt-2 text-xs text-gray-400">Dibuat oleh: {{ $report->createdBy->nama }} pada {{ $report->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Total Pengeluaran</p>
                <p class="text-xl font-bold text-gray-800">
                    {{ $report->mata_uang === 'USD' ? '$' : 'Rp' }} {{ number_format($report->total_amount, $report->mata_uang === 'USD' ? 2 : 0) }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Mata Uang</p>
                <p class="text-lg font-semibold">{{ $report->mata_uang }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Kurs (Ex-Rate)</p>
                <p class="text-lg font-semibold">{{ number_format($report->exrate) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Status Verifikasi</p>
                <p class="text-sm font-medium">
                    @if($report->verified_by)
                        <span class="text-green-600"><i class="fas fa-check mr-1"></i> Verified by {{ $report->verifiedBy->nama }}</span>
                    @else
                        <span class="text-gray-400">Belum diverifikasi</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Rincian Nota & Bukti</h3>
            <span class="text-xs text-gray-500">{{ $report->details->count() }} Item Transaksi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Tgl Nota</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Penerima</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-center">Unit</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Nota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($report->details as $detail)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $detail->invoice_date->format('d/m/y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $detail->item_desc }}</p>
                            @if($detail->explanation)
                                <p class="text-xs text-gray-500 mt-1 italic">{{ $detail->explanation }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $detail->recipient }}</td>
                        <td class="px-4 py-3 text-xs font-mono">
                            <span class="block">{{ $detail->place_code }}</span>
                            <span class="block text-gray-400">{{ $detail->exp_code }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $detail->unit_total }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($detail->unit_cost) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">{{ number_format($detail->actual) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($detail->file_nota)
                                <a href="{{ Storage::url($detail->file_nota) }}" target="_blank" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-file-image text-lg"></i>
                                </a>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t-2">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right">TOTAL</td>
                        <td class="px-4 py-3 text-right text-blue-800 text-lg">
                            {{ $report->mata_uang === 'USD' ? '$' : 'Rp' }} {{ number_format($report->total_amount, $report->mata_uang === 'USD' ? 2 : 0) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Review Info -->
    @if($report->notes_sa || $report->notes_fm)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($report->notes_sa)
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
            <h4 class="font-bold text-purple-800 text-sm mb-1 uppercase">Catatan Verifikasi (Staff Accountant)</h4>
            <p class="text-gray-700 text-sm">{{ $report->notes_sa }}</p>
        </div>
        @endif
        @if($report->notes_fm)
        <div class="bg-green-50 p-4 rounded-xl border border-green-200">
            <h4 class="font-bold text-green-800 text-sm mb-1 uppercase">Catatan Approval (Finance Manager)</h4>
            <p class="text-gray-700 text-sm">{{ $report->notes_fm }}</p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
