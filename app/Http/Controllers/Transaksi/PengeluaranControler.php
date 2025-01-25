<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran as ModelsPengeluaran;
use Illuminate\Http\Request;
USE RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;


use App\Models\Pengeluaran;
use App\Models\Lokasi;
use App\Exports\PengeluaranExport;

class PengeluaranControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $title = 'List Pengeluaran';

        $query = Pengeluaran::query();

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('search')) {
            $query->where('uraian', 'like', '%' . $request->search . '%');
        }

        $pengeluaran = $query->paginate(10); //entar malam task
        $lokasi = Lokasi::all();

        return view('pages.transaksi.pengeluaran.pengeluaran', compact('pengeluaran', 'lokasi', 'title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pengeluaran';

        return view('pages.transaksi.pengeluaran.tambah_pengeluaran', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $validateData = $request->validate([
            'tanggal' => 'nullable',
            'uraian' => 'nullable',
            'total' => 'nullable',
        ]);

        $validateData['tanggal'] = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
        $total = str_replace('.', '', $request->total);
        $validateData['total'] = $total;
        $validateData['id_lokasi'] = auth()->user()->id_lokasi;
        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Pengeluaran::create($validateData);

        Alert::success('Berhasil Menambahkan data Pengeluaran.');

        return redirect('/pengeluaran');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }
        $query = Pengeluaran::query();

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('search')) {
            $query->where('uraian', 'like', '%' . $request->search . '%');
        }


        $pengeluaran = $query->get();

        $title = "Detail Pengeluaran";

        return view('components.modal.modal_detail_data_pengeluaran', compact('title', 'pengeluaran'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Pengeluaran';

        $pengeluaran = Pengeluaran::findOrFail($id);

        return view('pages.transaksi.pengeluaran.edit_pengeluaran', compact('pengeluaran', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->tanggal);


        $validateData = $request->validate([
            'tanggal' => 'required',
            'uraian' => 'required',
            'total' => 'required',
        ]);

        $validateData['total'] = str_replace('.', '', $request->total);

        $validateData['tanggal'] = $request->tanggal;


        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update($validateData);

        Alert::success('Data Pengeluaran berhasil dihapus.');

        return redirect('/pengeluaran');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $pengeluaran->delete();

        Alert::success('Data Pengeluaran berhasil dihapus.');

        return redirect('/pengeluaran');
    }

    public function export(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }
        if ($request->filled('search')) {
            $query->where('uraian', 'like', '%' . $request->search . '%');
        }

        $pengeluaran = $query->get();

        return Excel::download(new PengeluaranExport($pengeluaran), 'pengeluaran.xlsx');
    }
}
