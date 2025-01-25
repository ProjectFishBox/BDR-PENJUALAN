<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\Pengeluaran as ModelsPengeluaran;
use Illuminate\Http\Request;
USE RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Carbon;

class PengeluaranControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pengeuaran';

        $pengeluaran = Pengeluaran::all();
        $search = trim($request->get('search'));

        $data = Pengeluaran::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('uraian', 'like', "%$search%")
                ->orWhere('tanggal', 'like', "%$search%");
            });
        })
        ->get();

        return view('pages.transaksi.pengeluaran.pengeluaran', compact('title', 'pengeluaran', 'data'));
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
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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

    public function modalDetail(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Detail Pengeluaran";

        $id = $request->input('id');

        $pengeluaran = Pengeluaran::findOrFail($id);

        return view('components.modal.modal_detail_data_pengeluaran', compact('title', 'pengeluaran'));
    }
}
