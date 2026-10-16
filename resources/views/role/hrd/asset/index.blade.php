@extends('layouts.main')

@section('page-title', 'Manajemen Asset')

@section('main-content')
    <style>
        .asset-card {
            border: 1px solid #eceef1;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(50, 50, 93, .05);
        }

        .asset-table > thead > tr > th {
            padding: 12px 16px;
            border-bottom: 1px solid #e9eaee;
            background: #f8f8fb;
            color: #a1acb8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .65px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .asset-table > tbody > tr > td {
            padding: 14px 16px;
            border-bottom: 1px solid #f2f3f5;
            color: #5d5f6e;
            font-size: 13px;
            vertical-align: middle;
        }

        .asset-action-btn {
            display: inline-flex;
            width: 32px;
            height: 32px;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .asset-code {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 6px;
            background: #f3f4f7;
            color: #565b69;
            font-family: monospace;
            font-size: 12px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Manajemen Asset</h4>
                <small class="text-muted">HRD &nbsp;/&nbsp; Asset &nbsp;·&nbsp; Kelola nama dan kode asset</small>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assetModal">
                <i class="bx bx-plus me-1"></i> Tambah Asset
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if ($errors->any() && !old('_form'))
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (request()->filled('search'))
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <small class="text-muted">Hasil pencarian untuk:</small>
                <span class="badge rounded-pill bg-primary">{{ request('search') }}</span>
                <a href="{{ route('asset.index') }}" class="btn btn-sm btn-outline-danger">Hapus filter</a>
            </div>
        @endif

        <div class="card asset-card overflow-hidden">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 border-bottom">
                <div>
                    <h5 class="mb-1">Daftar Asset</h5>
                    <small class="text-muted">Tambah dan ubah data asset</small>
                </div>
                <form action="{{ route('asset.index') }}" method="GET" class="w-100" style="max-width: 340px">
                    <label for="asset-search" class="visually-hidden">Cari asset</label>
                    <div class="input-group">
                        <input id="asset-search" type="search" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari nama atau kode asset..." autocomplete="off">
                        <button type="submit" class="btn btn-outline-primary" aria-label="Cari asset">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table asset-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:64px">#</th>
                            <th>Nama Asset</th>
                            <th>Kode Asset</th>
                            <th class="text-end" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $asset)
                            <tr>
                                <td class="text-muted">{{ $assets->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $asset->name }}</td>
                                <td><span class="asset-code">{{ $asset->code }}</span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary asset-action-btn js-edit-asset"
                                        data-bs-toggle="modal" data-bs-target="#assetModal"
                                        data-url="{{ route('asset.update', array_merge(['asset' => $asset->id], request()->query())) }}"
                                        data-id="{{ $asset->id }}" data-name="{{ $asset->name }}"
                                        data-code="{{ $asset->code }}" title="Ubah asset"
                                        aria-label="Ubah {{ $asset->name }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5 text-center">
                                    <h6 class="fw-semibold mb-1">
                                        {{ request()->filled('search') ? 'Asset tidak ditemukan' : 'Belum ada data asset' }}
                                    </h6>
                                    <p class="text-muted mb-3">
                                        {{ request()->filled('search') ? 'Coba kata kunci lain.' : 'Tambahkan asset pertama.' }}
                                    </p>
                                    @unless (request()->filled('search'))
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#assetModal">Tambah Asset</button>
                                    @endunless
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assets->total() > 0)
                <div class="px-4 py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $assets->firstItem() }}–{{ $assets->lastItem() }} dari {{ $assets->total() }} asset
                    </small>
                    @if ($assets->hasPages())
                        {{ $assets->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            @endif
        </div>

        @php
            $editingAssetId = old('_form') === 'edit_asset' ? old('asset_id') : null;
        @endphp
        <div class="modal fade" id="assetModal" tabindex="-1" aria-labelledby="assetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="assetForm" method="POST"
                        action="{{ $editingAssetId ? route('asset.update', array_merge(['asset' => $editingAssetId], request()->query())) : route('asset.store', request()->query()) }}">
                        @csrf
                        <input id="assetMethod" type="hidden" @if ($editingAssetId) name="_method" @endif
                            value="{{ $editingAssetId ? 'PUT' : '' }}">
                        <input id="assetFormMode" type="hidden" name="_form"
                            value="{{ $editingAssetId ? 'edit_asset' : 'create_asset' }}">
                        <input id="assetId" type="hidden" name="asset_id" value="{{ $editingAssetId }}">

                        <div class="modal-header border-bottom">
                            <div>
                                <h5 class="modal-title fw-semibold mb-0" id="assetModalLabel">
                                    {{ $editingAssetId ? 'Ubah Asset' : 'Tambah Asset' }}
                                </h5>
                                <small class="text-muted">Nama dan kode asset wajib diisi.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any() && old('_form'))
                                <div class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="assetName" class="form-label">Nama Asset <span class="text-danger">*</span></label>
                                <input id="assetName" type="text" name="name" class="form-control"
                                    value="{{ old('name') }}" maxlength="255" required placeholder="Contoh: Laptop">
                            </div>
                            <div>
                                <label for="assetCode" class="form-label">Kode Asset <span class="text-danger">*</span></label>
                                <input id="assetCode" type="text" name="code" class="form-control"
                                    value="{{ old('code') }}" maxlength="100" required placeholder="Contoh: AST-001">
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const assetForm = document.getElementById('assetForm');
        const assetModalLabel = document.getElementById('assetModalLabel');
        const assetModal = document.getElementById('assetModal');

        document.querySelectorAll('[data-bs-target="#assetModal"]:not(.js-edit-asset)').forEach(function (button) {
            button.addEventListener('click', function () {
                assetForm.reset();
                assetForm.action = @json(route('asset.store', request()->query()));
                document.getElementById('assetMethod').removeAttribute('name');
                document.getElementById('assetMethod').value = '';
                document.getElementById('assetFormMode').value = 'create_asset';
                document.getElementById('assetId').value = '';
                assetModalLabel.textContent = 'Tambah Asset';
            });
        });

        document.querySelectorAll('.js-edit-asset').forEach(function (button) {
            button.addEventListener('click', function () {
                assetForm.action = button.dataset.url;
                document.getElementById('assetMethod').setAttribute('name', '_method');
                document.getElementById('assetMethod').value = 'PUT';
                document.getElementById('assetFormMode').value = 'edit_asset';
                document.getElementById('assetId').value = button.dataset.id;
                document.getElementById('assetName').value = button.dataset.name;
                document.getElementById('assetCode').value = button.dataset.code;
                assetModalLabel.textContent = 'Ubah Asset';
            });
        });

        assetModal.addEventListener('shown.bs.modal', function () {
            document.getElementById('assetName').focus();
        });

        @if ($errors->any() && old('_form'))
            document.addEventListener('DOMContentLoaded', function () {
                @if (old('_form') === 'edit_asset')
                    const editButton = document.querySelector('.js-edit-asset[data-id="{{ old('asset_id') }}"]');
                    if (editButton) {
                        editButton.click();
                        document.getElementById('assetName').value = @json(old('name', ''));
                        document.getElementById('assetCode').value = @json(old('code', ''));
                    }
                @endif
                bootstrap.Modal.getOrCreateInstance(assetModal).show();
            });
        @endif
    </script>
@endsection