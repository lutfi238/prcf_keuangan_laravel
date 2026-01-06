@extends('layouts.app')

@section('title', 'Detail Buku Piutang')
@section('page-title', 'Buku Piutang - ' . date('F', mktime(0, 0, 0, $header->periode_bulan, 1)) . ' ' . $header->periode_tahun)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('books.receivables.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                {{ $header->kode_proyek }}
            </span>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-xs font-bold text-gray-500 uppercase">Beginning Balance (USD)</p>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($header->beginning_balance_usd, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border bg-blue-50">
            <p class="text-xs font-bold text-blue-600 uppercase">Ending Balance (USD)</p>
            <p class="text-2xl font-bold text-blue-800">${{ number_format($header->ending_balance_usd, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-xs font-bold text-gray-500 uppercase">Ending Balance (IDR)</p>
            <p class="text-xl font-bold text-gray-700">Rp {{ number_format($header->ending_balance_idr, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Rincian Transaksi Piutang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-100 text-gray-600 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Voucher/Reff</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">Recipient</th>
                        <th class="px-4 py-3 text-right">Debit (USD)</th>
                        <th class="px-4 py-3 text-right">Credit (USD)</th>
                        <th class="px-4 py-3 text-right bg-blue-50">Balance (USD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-gray-50 font-semibold italic text-gray-500">
                        <td colspan="6" class="px-4 py-2 text-right text-xs">Beginning Balance</td>
                        <td class="px-4 py-2 text-right text-xs">${{ number_format($header->beginning_balance_usd, 2) }}</td>
                    </tr>
                    @foreach($header->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $detail->tgl_trx->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $detail->reff }}</td>
                            <td class="px-4 py-3">
                                <p class="text-gray-800">{{ $detail->description }}</p>
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
