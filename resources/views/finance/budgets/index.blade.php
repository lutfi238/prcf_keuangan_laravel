@extends('layouts.app')

@section('title', 'Kelola Budget Desa')
@section('page-title', 'Kelola Budget Desa')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Input -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 border sticky top-24">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Input / Update Budget</h2>
            <form action="{{ route('finance.budgets.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proyek *</label>
                    <select name="kode_proyek" id="kode_proyek" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->kode_proyek }}">{{ $project->kode_proyek }} - {{ $project->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desa *</label>
                    <select name="id_village" id="id_village" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Desa</option>
                        @foreach($villages as $village)
                            <option value="{{ $village->id_village }}" data-abbr="{{ $village->village_abbr }}">
                                {{ $village->village_name }} ({{ $village->village_abbr }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exp Code *</label>
                    <select name="exp_code" id="exp_code" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Proyek Terlebih Dahulu</option>
                    </select>
                </div>

                <div class="bg-gray-50 p-3 rounded border">
                    <label class="block text-xs font-bold text-gray-500 uppercase">Place Code Preview</label>
                    <p id="place_code_preview" class="font-mono text-lg font-bold text-blue-600">-</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Uang</label>
                        <select name="currency" id="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="USD">USD</option>
                            <option value="IDR">IDR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Rate</label>
                        <input type="number" step="0.01" name="exrate" id="exrate" value="15500" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Budget *</label>
                    <input type="number" step="0.01" name="amount" id="amount" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="bg-blue-50 p-3 rounded text-sm text-blue-800">
                    <p>Estimasi Konversi:</p>
                    <p id="conversion_preview" class="font-bold">-</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Simpan Budget
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Daftar Budget</h2>
                <form method="GET" class="flex space-x-2">
                    <select name="kode_proyek" class="text-sm border rounded px-2 py-1" onchange="this.form.submit()">
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->kode_proyek }}" {{ request('kode_proyek') == $project->kode_proyek ? 'selected' : '' }}>
                                {{ $project->kode_proyek }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Place Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Budget (USD)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Used</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($budgets as $budget)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono font-medium">
                                    {{ $budget->place_code }}
                                    <div class="text-xs text-gray-500">{{ $budget->kode_proyek }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $budget->village->village_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    ${{ number_format($budget->budget_usd, 2) }}
                                    <div class="text-xs text-gray-500">Rp {{ number_format($budget->budget_idr, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">${{ number_format($budget->used_usd ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="{{ ($budget->remaining_usd ?? $budget->budget_usd) < 0 ? 'text-red-600' : 'text-green-600' }} font-bold">
                                        ${{ number_format($budget->remaining_usd ?? $budget->budget_usd, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('finance.budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Hapus budget ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data budget.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($budgets->hasPages())
                <div class="px-4 py-3 border-t">{{ $budgets->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeProyekSelect = document.getElementById('kode_proyek');
    const expCodeSelect = document.getElementById('exp_code');
    const villageSelect = document.getElementById('id_village');
    const placeCodePreview = document.getElementById('place_code_preview');
    const amountInput = document.getElementById('amount');
    const currencySelect = document.getElementById('currency');
    const exrateInput = document.getElementById('exrate');
    const conversionPreview = document.getElementById('conversion_preview');

    async function fetchExpCodes(kodeProyek) {
        try {
            const response = await fetch(`{{ route('finance.budgets.exp-codes') }}?kode_proyek=${kodeProyek}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching exp codes:', error);
            return [];
        }
    }

    kodeProyekSelect.addEventListener('change', async function() {
        const kodeProyek = this.value;
        expCodeSelect.innerHTML = '<option value="">Loading...</option>';
        
        if (!kodeProyek) {
            expCodeSelect.innerHTML = '<option value="">Pilih Proyek Terlebih Dahulu</option>';
            return;
        }

        const data = await fetchExpCodes(kodeProyek);
        if (data.length === 0) {
            expCodeSelect.innerHTML = '<option value="">Tidak ada Exp Code ditemukan</option>';
        } else {
            let options = '<option value="">Pilih Exp Code</option>';
            data.forEach(item => {
                options += `<option value="${item.exp_code}">${item.exp_code}</option>`;
            });
            expCodeSelect.innerHTML = options;
        }
        updatePlaceCode();
    });

    function updatePlaceCode() {
        const expCode = expCodeSelect.value;
        const selectedOption = villageSelect.options[villageSelect.selectedIndex];
        const abbr = selectedOption?.getAttribute('data-abbr');
        
        if (expCode && abbr) {
            placeCodePreview.textContent = expCode + '-' + abbr + '-01';
        } else {
            placeCodePreview.textContent = '-';
        }
    }

    function updateConversion() {
        const amount = parseFloat(amountInput.value) || 0;
        const exrate = parseFloat(exrateInput.value) || 1;
        const currency = currencySelect.value;
        
        if (currency === 'USD') {
            const idr = amount * exrate;
            conversionPreview.textContent = '≈ Rp ' + new Intl.NumberFormat('id-ID').format(idr);
        } else {
            const usd = amount / exrate;
            conversionPreview.textContent = '≈ $' + usd.toFixed(2);
        }
    }

    expCodeSelect.addEventListener('change', updatePlaceCode);
    villageSelect.addEventListener('change', updatePlaceCode);
    amountInput.addEventListener('input', updateConversion);
    exrateInput.addEventListener('input', updateConversion);
    currencySelect.addEventListener('change', updateConversion);
});
</script>
@endpush
@endsection
