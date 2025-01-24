<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;


class BarangControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Barang';

        $search = $request->get('search');

        $data = Barang::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%$search%");
        })->get();

        return view('pages.master.barang.barang', compact('title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Barang';

        return view('pages.master.barang.tambah_barang', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ]);

        $harga = str_replace('.', '', $request->harga);
        $validateData['harga'] = $harga;

        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Barang::create($validateData);

        Alert::success('Berhasil Menambahkan data barang.');
        return redirect('/barang');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Barang';

        $barang = Barang::findOrFail($id);

        return view('pages.master.barang.edit_barang', compact('barang', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = $request->validate([
            'kode_barang' => 'required',
            'nama' => 'required',
            'merek' => 'required',
            'harga' => 'required',
        ]);

        $barang = Barang::findOrFail($id);

        $barang->update([
            'kode_barang' => $validateData['kode_barang'],
            'nama' => $validateData['nama'],
            'merek' => $validateData['merek'],
            'harga' => str_replace('.', '', $validateData['harga']),
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Merubah data Barang.');

        return redirect('/barang');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $barang = Barang::findOrFail($id);

            $barang->delete();

            Alert::success('Data Barang berhasil dihapus.');

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }

    public function modalImport(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Import Barang";

        $action = "barang.import-file";

        $type = 'barang';

        return view('components.modal.modal_import_data', compact('title', 'action', 'type'));
    }

    public function downloadTamplate()
    {
        $filePath = public_path('import_tamplate/tamplate_barang.csv');
        $fileName = 'template_barang_.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $fileName);
    }


    public function importBarang(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }


        try {
            $userId = auth()->id();

            $file = $request->file('customFile');
            $fileName = "BarangImport-" . date('YmdHis') . '-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('files', $fileName);

            Excel::import(new BarangImport($userId), $filePath);

            return response()->json(['code' => 200, 'success' => 'Data berhasil diimpor!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 400, 'error' => $e->getMessage()]);
        }
    }



}
