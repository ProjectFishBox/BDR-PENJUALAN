<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\SetHargaImport;
use Illuminate\Http\Request;

use App\Models\Barang;
use App\Models\SetHarga;
use App\Models\Lokasi;

use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;

class SetHargaControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Set Harga';

        $search = trim($request->get('search'));
        $lokasiId = $request->get('lokasi');

        $lokasiList = Lokasi::all();

        $data = SetHarga::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%$search%")
                    ->orWhere('kode_barang', 'like', "%$search%");
                });
            })
            ->when($lokasiId, function ($query, $lokasiId) {
                return $query->where('id_lokasi', $lokasiId);
            })
            ->with('lokasi')
            ->get();

        return view('pages.master.set_harga.set_harga', compact('title', 'data', 'lokasiList'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Set Harga';

        $barang = Barang::all();

        return view('pages.master.set_harga.tambah_set_harga', compact('title', 'barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|max:50',
            'harga' => 'required',
            'kode_barang' => 'nullable',
            'untung' => 'required',
            'merek' => 'required|max:50',
            'harga_jual' => 'required'
        ]);

        $getNamaBarang = Barang::findOrFail($request->nama_barang);

        $harga = str_replace('.', '', $request->harga);
        $untung = str_replace('.', '', $request->untung);
        $harga_jual = str_replace('.', '', $request->harga_jual);
        $status = $request->status === 'on' ? 'Aktif' : 'Tidak Aktif';


        SetHarga::create([
            'id_lokasi' => auth()->user()->id_lokasi,
            'id_barang' => $request->nama_barang,
            'nama_barang' => $getNamaBarang->nama,
            'kode_barang' => $request->kode_barang,
            'merek' => $request->merek,
            'harga' => (int) $harga,
            'untung' => (int) $untung,
            'harga_jual' => (int) $harga_jual,
            'status' => $status,
            'create_by' => auth()->id(),
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Menambahkan data Set Harga.');
        return redirect('/setharga');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Set Harga';

        $setharga = SetHarga::findOrFail($id);
        $barang = Barang::all();

        return view('pages.master.set_harga.edit_set_harga', compact('setharga', 'title', 'barang'));
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
        $validatedData = $request->validate([
            'nama_barang' => 'required|max:50',
            'harga' => 'required|max:50',
            'kode_barang' => 'nullable',
            'untung' => 'required',
            'merek' => 'required|max:50',
            'harga_jual' => 'required|max:50'
        ]);

        $validatedData['last_user'] = auth()->id();
        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;

        $getNamaBarang = Barang::findOrFail($request->nama_barang);

        $validatedData['nama_barang'] = $getNamaBarang->nama;

        $status = 'Tidak Aktif';
        if($request->status === 'on'){
            $status = 'Aktif';
        }

        $validatedData['status'] = $status;

        $setharga = SetHarga::findOrFail($id);

        $setharga->update([
            'id_lokasi' => auth()->user()->id_lokasi,
            'id_barang' => $request->nama_barang,
            'nama_barang' => $getNamaBarang->nama,
            'kode_barang' => $request->kode_barang,
            'merek' => $request->merek,
            'harga' => $request->harga,
            'untung' => $request->untung,
            'harga_jual' => $request->harga_jual,
            'status' => $status,
            'create_by' => auth()->id(),
            'last_user' => auth()->id(),
        ]);


        Alert::success('Berhasil Merubah data SET Harga.');

        return redirect('/setharga');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setharga = SetHarga::findOrFail($id);

        $setharga->delete();

        Alert::success('Data Set Harga berhasil dihapus.');

        return redirect('/setharga');
    }

    public function modalImport(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Import Set Harga";

        $action = "import-setharga";

        $type = 'setharga';

        return view('components.modal.modal_import_data', compact('title', 'action', 'type'));
    }

    public function downloadTamplate()
    {
        $filePath = public_path('import_tamplate/tamplate_setharga.csv');
        $fileName = 'template_setharga_.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $fileName);
    }

    public function importSetHarga(Request $request)
    {

        try {
            Excel::import(new SetHargaImport, $request->file('customFile'));

            Alert::success('Berhasil', 'Data berhasil diimport.');

            return response()->json(['code' => 200, 'success' => 'Data berhasil diimpor!']);
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
