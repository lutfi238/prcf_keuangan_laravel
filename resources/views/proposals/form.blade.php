@extends('layouts.app')

@section('title', isset($proposal) ? 'Edit Proposal' : 'Buat Proposal Baru')
@section('page-title', isset($proposal) ? 'Edit Proposal' : 'Buat Proposal Baru')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('proposals.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <form action="{{ isset($proposal) ? route('proposals.update', $proposal->id_proposal) : route('proposals.store') }}" 
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($proposal))
            @method('PUT')
        @endif

        <!-- General Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Umum</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Proposal *</label>
                    <input type="text" name="judul_proposal" value="{{ old('judul_proposal', $proposal->judul_proposal ?? '') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proyek *</label>
                    <select name="kode_proyek" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->kode_proyek }}" {{ (old('kode_proyek', $proposal->kode_proyek ?? '') == $project->kode_proyek) ? 'selected' : '' }}>
                                {{ $project->kode_proyek }} - {{ $project->nama_proyek }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ old('date', isset($proposal) ? $proposal->date->format('Y-m-d') : date('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab (PJ) *</label>
                    <input type="text" name="pj" value="{{ old('pj', $proposal->pj ?? '') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemohon *</label>
                    <input type="text" name="pemohon" value="{{ old('pemohon', $proposal->pemohon ?? auth()->user()->nama) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- File Upload Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Dokumen Pendukung</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File TOR (PDF) {{ !isset($proposal) ? '*' : '' }}</label>
                    <input type="file" name="tor" accept=".pdf" {{ !isset($proposal) ? 'required' : '' }}
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @if(isset($proposal) && $proposal->tor)
                        <p class="mt-1 text-xs text-gray-500">File saat ini: <a href="{{ Storage::url($proposal->tor) }}" target="_blank" class="text-blue-600 underline">Lihat TOR</a></p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Budget (Excel) {{ !isset($proposal) ? '*' : '' }}</label>
                    <input type="file" name="file_budget" accept=".xlsx,.xls" {{ !isset($proposal) ? 'required' : '' }}
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @if(isset($proposal) && $proposal->file_budget)
                        <p class="mt-1 text-xs text-gray-500">File saat ini: <a href="{{ Storage::url($proposal->file_budget) }}" target="_blank" class="text-blue-600 underline">Lihat Budget</a></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Budget Details Card (Alpine.js) -->
        <div class="bg-white rounded-xl shadow-sm p-6" x-data="budgetManager()">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="text-lg font-semibold">Rincian Anggaran</h3>
                <button type="button" @click="addRow()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b">
                            <th class="px-2 py-2 text-left w-48">Desa *</th>
                            <th class="px-2 py-2 text-left w-32">Exp Code *</th>
                            <th class="px-2 py-2 text-left">Deskripsi</th>
                            <th class="px-2 py-2 text-right w-32">Budget USD *</th>
                            <th class="px-2 py-2 text-right w-40">Budget IDR *</th>
                            <th class="px-2 py-2 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-2 py-2">
                                    <select :name="`budget_details[${index}][id_village]`" required class="w-full border-gray-300 rounded p-1">
                                        <option value="">Pilih Desa</option>
                                        @foreach($villages as $village)
                                            <option value="{{ $village->id_village }}">{{ $village->id_village }} - {{ $village->village_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`budget_details[${index}][exp_code]`" x-model="row.exp_code" required
                                           class="w-full border-gray-300 rounded p-1" placeholder="cth: 5310">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" :name="`budget_details[${index}][description]`" x-model="row.description"
                                           class="w-full border-gray-300 rounded p-1">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" step="0.01" :name="`budget_details[${index}][requested_usd]`" x-model.number="row.usd" required
                                           class="w-full border-gray-300 rounded p-1 text-right">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" :name="`budget_details[${index}][requested_idr]`" x-model.number="row.idr" required
                                           class="w-full border-gray-300 rounded p-1 text-right">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td colspan="3" class="px-2 py-2 text-right">TOTAL</td>
                            <td class="px-2 py-2 text-right text-blue-600" x-text="formatCurrency(totalUsd(), '$')"></td>
                            <td class="px-2 py-2 text-right text-blue-600" x-text="formatCurrency(totalIdr(), 'Rp ')"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="button" onclick="history.back()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg">
                <i class="fas fa-save mr-2"></i> {{ isset($proposal) ? 'Update Proposal' : 'Simpan Proposal' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function budgetManager() {
        @php
            $existingDetails = [];
            if (isset($proposal) && $proposal->budgetDetails) {
                foreach ($proposal->budgetDetails as $d) {
                    $existingDetails[] = [
                        'id_village' => $d->id_village,
                        'exp_code' => $d->exp_code,
                        'description' => $d->description,
                        'usd' => $d->requested_usd,
                        'idr' => $d->requested_idr,
                    ];
                }
            }
            if (empty($existingDetails)) {
                $existingDetails = [['id_village' => '', 'exp_code' => '', 'description' => '', 'usd' => 0, 'idr' => 0]];
            }
        @endphp
        return {
            rows: @json($existingDetails),
            
            addRow() {
                this.rows.push({id_village: '', exp_code: '', description: '', usd: 0, idr: 0});
            },
            
            removeRow(index) {
                if(this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },
            
            totalUsd() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.usd) || 0), 0);
            },
            
            totalIdr() {
                return this.rows.reduce((sum, row) => sum + (parseInt(row.idr) || 0), 0);
            },
            
            formatCurrency(val, symbol) {
                return symbol + new Intl.NumberFormat('id-ID').format(val);
            }
        }
    }
</script>
@endpush
@endsection
