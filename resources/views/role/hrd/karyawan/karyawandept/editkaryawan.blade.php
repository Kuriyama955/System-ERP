@extends('layouts.main')

@section('page-title', 'Form Edit Karyawan')

@section('main-content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
<div class="container-xxl flex-grow-1 container-p-y">
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="row" id='row'>
            <div class="col-12 stretch-card grid-margin" id="myForm">
                <div class="border rounded card">
                    <div class="border rounded shadow card-body">
                        <h4 class="mb-3 card-title">Form Edit Employee</h4>
                        <form class="form-sample" method="POST" action="{{ route('karyawan.update', $karyawan->id) }}"
                            id="pricelistForm">
                            @method('PUT')
                            @csrf
                            <div id='row2'>
                                <div class="shadow row" style="">
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">Nama</label>
                                            <input type="text" class="form-control form-control-sm clickable"
                                                name="nama" required value="{{ $karyawan->nama }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">NPK</label>
                                            <input type="text" class="form-control form-control-sm clickable"
                                                name="npk" required value="{{ $karyawan->npk }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">Departement</label>
                                            <select class="form-select form-select-sm" name="departement" required>
                                                <option value="" disabled>Pilih Departement</option>
                                                @foreach ($departements as $departement)
                                                    <option value="{{ $departement }}"
                                                        {{ old('departement', $karyawan->departement) === $departement ? 'selected' : '' }}>
                                                        {{ $departement }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">Jabatan</label>
                                            <input type="text" class="form-control form-control-sm clickable"
                                                name="jabatan" required value="{{ $karyawan->jabatan }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">Golongan Jabatan</label>
                                            <input type="text" class="form-control form-control-sm clickable"
                                                name="golonganjabatan" required value="{{ $karyawan->golonganjabatan }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md ">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold">Bagian</label>
                                            <input type="text" class="form-control form-control-sm clickable"
                                                name="bagian" required value="{{ $karyawan->bagian }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold ">Tanggal Masuk</label>
                                            <input type="date" class="form-control form-control-sm clickable"
                                                name="tglmasuk" placeholder="" required value="{{ $karyawan->tglmasuk }}">
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md">
                                        <div class="mb-3" style="font-size: 14px">
                                            <label for="" class="form-label fw-bold ">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="AKTIF">AKTIF</option>
                                                <option value="TIDAKAKTIF">TIDAK AKTIF</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="mt-2 btn-rounded btn btn-gradient-info float-end">
                                    Submit
                                </button>
                            </div>
                            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>




    @endsection