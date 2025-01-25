@extends('components._partials.layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $title }}</h4>
                @csrf
                <div class="form-group">
                    <label for="tanggal">Tanggal <span style="color: red">*</span></label>
                    <input type="text" class="form-control datepicker-input" placeholder="Piih Tanggal" name="tanggal" >
                </div>
                <div class="form-group">
                    <label for="uraian">Uraian <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="uraian" placeholder="uraian Barang" name="uraian" >
                </div>
                <div class="form-group">
                    <label for="total">Total <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="total" placeholder="Total" name="total" >
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end">
                        <a href="/pengeluaran" class="btn btn-danger mr-3">Batal</a>
                        <button class="btn btn-success" type="submit">Simpan</button>
                    </div>
                </div>
        </div>
    </div>

@endsection

