@extends('layouts.app')

@section('title', 'Detail Buku Bank')
@section('page-title', 'Buku Bank - ' . date('F', mktime(0, 0, 0, $header->periode_bulan, 1)) . ' ' . $header->periode_tahun)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('books.bank.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                {{ $header->kode_proyek }}
            </span>
            <span class="px-3 py-1 text-sm rounded-full {{ $header->status_laporan === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                {{ ucfirst($header->status_laporan) }}
            </span>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-xs font-bold text-gray-500 uppercase">Saldo Awal (USD)</p>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($header->saldo_awal_usd, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Perubahan (USD)</p>
            <p class="text-2xl font-bold {{ $header->current_period_change_usd >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $header->current_period_change_usd >= 0 ? '+' : '' }}${{ number_format($header->current_period_change_usd, 2) }}
            </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border bg-blue-50">
            <p class="text-xs font-bold text-blue-600 uppercase">Saldo Akhir (USD)</p>
            <p class="text-2xl font-bold text-blue-800">${{ number_format($header->saldo_akhir_usd, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-xs font-bold text-gray-500 uppercase">Saldo Akhir (IDR)</p>
            <p class="text-xl font-bold text-gray-700">Rp {{ number_format($header->saldo_akhir_idr, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700">Rincian Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-100 text-gray-600 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Reff</th>
                        <th class="px-4 py-3 text-left">Activity / Description</th>
                        <th class="px-4 py-3 text-left">Recipient</th>
                        <th class="px-4 py-3 text-right">Debit (USD)</th>
                        <th class="px-4 py-3 text-right">Credit (USD)</th>
                        <th class="px-4 py-3 text-right bg-blue-50">Balance (USD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-gray-50 font-semibold italic text-gray-500">
                        <td colspan="6" class="px-4 py-2 text-right">Saldo Awal</td>
                        <td class="px-4 py-2 text-right">${{ number_format($header->saldo_awal_usd, 2) }}</td>
                    </tr>
                    @foreach($header->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $detail->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $detail->reff }}</td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="font-bold text-gray-800">{{ $detail->title_activity }}</p>
                                <p class="text-gray-500 leading-tight">{{ $detail->cost_description }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $detail->recipient }}</td>
                            <td class="px-4 py-3 text-right text-green-600">
                                {{ $detail->debit_usd > 0 ? '$' . number_format($detail->debit_usd, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right text-red-600">
                                {{ $detail->credit_usd > 0 ? '$' . number_format($detail->credit_usd, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold bg-blue-50/50">
                                ${{ number_format($detail->balance_usd, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
