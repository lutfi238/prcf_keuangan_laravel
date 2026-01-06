@extends('layouts.app')

@section('title', isset($report) ? 'Edit Laporan Keuangan' : 'Buat Laporan Baru')
@section('page-title', isset($report) ? 'Edit Laporan Keuangan' : 'Buat Laporan Keuangan Baru')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ isset($report) ? route('reports.update', $report->id_laporan_keu) : route('reports.store') }}" 
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($report))
            @method('PUT')
        @endif

        <!-- General Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proyek *</label>
                    <select name="kode_proyek" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" {{ isset($report) ? 'disabled bg-gray-50' : '' }}>
                        <option value="">Pilih Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->kode_proyek }}" {{ (old('kode_proyek', $report->kode_proyek ?? '') == $project->kode_proyek) ? 'selected' : '' }}>
                                {{ $project->kode_proyek }}
                            </option>
                        @endforeach
                    </select>
                    @if(isset($report))
                        <input type="hidden" name="kode_proyek" value="{{ $report->kode_proyek }}">
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan *</label>
                    <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $report->nama_kegiatan ?? '') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelaksana *</label>
                    <input type="text" name="pelaksana" value="{{ old('pelaksana', $report->pelaksana ?? auth()->user()->nama) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan *</label>
                    <input type="date" name="tanggal_pelaksanaan" value="{{ old('tanggal_pelaksanaan', isset($report) ? $report->tanggal_pelaksanaan->format('Y-m-d') : '') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Laporan *</label>
                    <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', isset($report) ? $report->tanggal_laporan->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Uang *</label>
                    <select name="mata_uang" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="IDR" {{ (old('mata_uang', $report->mata_uang ?? 'IDR') == 'IDR') ? 'selected' : '' }}>IDR</option>
                        <option value="USD" {{ (old('mata_uang', $report->mata_uang ?? '') == 'USD') ? 'selected' : '' }}>USD</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kurs (Exchange Rate) *</label>
                    <input type="number" name="exrate" value="{{ old('exrate', $report->exrate ?? '15000') }}" required
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded-xl shadow-sm p-6 border" x-data="reportDetailsManager()">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="text-lg font-semibold">Rincian Transaksi</h3>
                <button type="button" @click="addRow()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b">
                            <th class="px-2 py-2 text-left w-32">Tanggal Nota *</th>
                            <th class="px-2 py-2 text-left">Deskripsi / Item *</th>
                            <th class="px-2 py-2 text-left w-40">Penerima *</th>
                            <th class="px-2 py-2 text-center w-20">Unit *</th>
                            <th class="px-2 py-2 text-right w-28">Harga Satuan *</th>
                            <th class="px-2 py-2 text-right w-32">Total</th>
                            <th class="px-2 py-2 text-left w-32">Nota / Bukti</th>
                            <th class="px-2 py-2 text-center w-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr class="border-b hover:bg-gray-50 align-top">
                                <td class="px-2 py-2">
                                    <input type="date" :name="`details[${index}][invoice_date]`" x-model="row.invoice_date" required class="w-full border-gray-300 rounded p-1">
                                </td>
                                <td class="px-2 py-2">
                                    <textarea :name="`details[${index}][item_desc]`" x-model="row.item_desc" required rows="1" class="w-full border-gray-300 rounded p-1"></textarea>
                                    <div class="mt-1 flex space-x-1">
                                        <input type="text" :name="`details[${index}][place_code]`" x-model="row.place_code" placeholder="P_Code" class="w-1/2 border-gray-300 rounded p-1 text-[10px]">
                                        <input type="text" :name="`details[${index}][exp_code]`" x-model="row.exp_code" placeholder="E_Code" class="w-1/2 border-gray-300 rounded p-1 text-[10px]">
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`details[${index}][recipient]`" x-model="row.recipient" required class="w-full border-gray-300 rounded p-1 font-semibold">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <input type="number" :name="`details[${index}][unit_total]`" x-model.number="row.unit_total" required class="w-full border-gray-300 rounded p-1 text-center">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" step="0.01" :name="`details[${index}][unit_cost]`" x-model.number="row.unit_cost" required class="w-full border-gray-300 rounded p-1 text-right">
                                </td>
                                <td class="px-2 py-2 text-right font-bold text-gray-700">
                                    <span x-text="formatNumber(row.unit_total * row.unit_cost)"></span>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="file" :name="`details[${index}][file_nota]`" accept="image/*,.pdf" class="w-full text-[10px]">
                                    <template x-if="row.id && row.file_nota">
                                        <div class="mt-1 text-blue-600 underline text-[10px]">Nota ada</div>
                                    </template>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                            <td colspan="5" class="px-2 py-2 text-right">TOTAL LAPORAN</td>
                            <td class="px-2 py-2 text-right text-lg text-blue-700" x-text="formatNumber(total())"></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="button" onclick="history.back()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg">
                <i class="fas fa-save mr-2"></i> {{ isset($report) ? 'Simpan Laporan' : 'Kirim Laporan' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function reportDetailsManager() {
        @php
            $existingDetails = [];
            if (isset($report) && $report->details) {
                foreach ($report->details as $d) {
                    $existingDetails[] = [
                        'id' => $d->id_detail_keu ?? null,
                        'invoice_date' => $d->invoice_date ? $d->invoice_date->format('Y-m-d') : date('Y-m-d'),
                        'item_desc' => $d->item_desc,
                        'recipient' => $d->recipient,
                        'unit_total' => $d->unit_total,
                        'unit_cost' => $d->unit_cost,
                        'place_code' => $d->place_code,
                        'exp_code' => $d->exp_code,
                        'file_nota' => $d->file_nota,
                    ];
                }
            }
            if (empty($existingDetails)) {
                $existingDetails = [[
                    'invoice_date' => date('Y-m-d'),
                    'item_desc' => '',
                    'recipient' => '',
                    'unit_total' => 1,
                    'unit_cost' => 0,
                    'place_code' => '',
                    'exp_code' => '',
                    'file_nota' => null
                ]];
            }
        @endphp
        return {
            rows: @json($existingDetails),
            
            addRow() {
                this.rows.push({
                    invoice_date: new Date().toISOString().split('T')[0],
                    item_desc: '',
                    recipient: '',
                    unit_total: 1,
                    unit_cost: 0,
                    place_code: '',
                    exp_code: '',
                    file_nota: null
                });
            },
            
            removeRow(index) {
                if(this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },
            
            total() {
                return this.rows.reduce((sum, row) => sum + ((parseFloat(row.unit_total) * parseFloat(row.unit_cost)) || 0), 0);
            },
            
            formatNumber(val) {
                return new Intl.NumberFormat('id-ID').format(val);
            }
        }
    }
</script>
@endpush
@endsection
