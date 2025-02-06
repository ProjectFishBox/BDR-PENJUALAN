<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use App\Models\Lokasi;
use App\Models\Pelanggan;


class PelangganControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $title = "List Pelanggan";
        $cacheKey = 'pelanggan_data';
        $cacheDuration = now()->addMinutes(3);

        $lokasiList = Cache::remember('lokasiList', now()->addMinutes(3), function () {
            return Lokasi::all();
        });

        if ($request->ajax()) {

            $lokasiId = $request->get('lokasi');

            if (!$lokasiId) {
                $data = Cache::remember($cacheKey, $cacheDuration, function () {
                    return Pelanggan::with('lokasi')
                        ->where('delete', 0)
                        ->get();
                });
            } else {
                $data = Pelanggan::when($lokasiId, function ($query, $lokasiId) {
                    return $query->where('id_lokasi', $lokasiId);
                })
                ->with('lokasi')
                ->where('delete', 0)
                ->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button class="btn btn-icon btn-primary btn-pelanggan-edit gap-5" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-danger btn-pelanggan-delete" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-delete"></i>
                        </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master.pelanggan.pelanggan', compact('title', 'lokasiList'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pelanggan';

        $lokasi = Lokasi::all();

        return view('pages.master.pelanggan.tambah_pelanggan', compact('title', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'id_kota' => 'required|integer',
            'fax' => 'string|max:255',
            'kode_pos' => 'string|max:255'
        ]);
        $validateData['id_lokasi'] = auth()->user()->id_lokasi;
        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Pelanggan::create($validateData);

        Cache::forget('pelanggan_data');

        Alert::success('Berhasil Menambahkan data Pelanggan.');
        return redirect('/pelanggan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Pelanggan';

        $pelanggan = Pelanggan::findOrFail($id);
        $lokasi = Lokasi::all();

        return view('pages.master.pelanggan.edit_pelanggan', compact('title', 'pelanggan', 'lokasi'));
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
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'id_kota' => 'required|integer',
        ]);

        $validateData['id_lokasi'] = auth()->user()->id;
        $validateData['last_user'] = auth()->id();

        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kode_pos' => $request->kode_pos,
            'telepon' => $request->telepon,
            'fax' => $request->fax,
            'id_kota' => $request->id_kota,
            'id_lokasi' => auth()->user()->id_lokasi,
            'last_user' => auth()->id(),
        ]);

        Cache::forget('pelanggan_data');

        Alert::success('Berhasil Merubah data Pelanggan.');

        return redirect('/pelanggan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $pelanggan->update([
                'delete' => 1,
                'last_user' => auth()->id()
            ]);

            Cache::forget('pelanggan_data');

            Alert::success('Data Lokasi berhasil dihapus.');

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }
}
