<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

use App\Models\Lokasi;
use App\Models\Pelanggan;
use Laravolt\Indonesia\Facade as Indonesia;

class PelangganControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = "List Pelanggan";

        $lokasiList = Lokasi::where('delete', 0)->get();

        if ($request->ajax()) {

            $lokasiId = $request->get('lokasi');

            if (!$lokasiId) {
                $data = Pelanggan::with('lokasi')
                        ->where('delete', 0)
                        ->orderBy('created_at', 'desc')
                        ->get();
            } else {
                $query = Pelanggan::with('lokasi')->where('delete', 0)->orderBy('created_at', 'desc');

                if ($lokasiId && $lokasiId !== 'all') {
                    $query->where('id_lokasi', $lokasiId);
                }

                $data = $query->get();

            }

            foreach ($data as $p) {
                $p->nama_kota = Indonesia::findCity($p->id_kota)['name'] ?? 'Tidak Diketahui';
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

        $lokasi = Lokasi::where('delete', 0)->get();
        $kota = Indonesia::allCities();

        return view('pages.master.pelanggan.tambah_pelanggan', compact('title', 'lokasi', 'kota'));
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
        $lokasi = Lokasi::where('delete', 0)->get();
        $kota = Indonesia::allCities();

        return view('pages.master.pelanggan.edit_pelanggan', compact('title', 'pelanggan', 'lokasi', 'kota'));
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
