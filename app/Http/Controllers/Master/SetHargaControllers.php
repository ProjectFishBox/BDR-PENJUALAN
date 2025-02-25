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
use Yajra\DataTables\DataTables;


class SetHargaControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Set Harga';

        $lokasiList = Lokasi::where('delete', 0)->get();

        if ($request->ajax()) {

            $lokasiId = $request->get('lokasi');

            if (!$lokasiId) {
                $data = SetHarga::with('lokasi')
                        ->where('delete', 0)
                        ->orderBy('created_at', 'desc')
                        ->get();
            } else {
                $data = SetHarga::with('lokasi')->where('delete', 0)->orderBy('created_at', 'desc');

                if ($lokasiId && $lokasiId !== 'all') {
                    $data->where('id_lokasi', $lokasiId);
                }

                $data = $data->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button class="btn btn-icon btn-primary btn-setharga-edit gap-5" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-danger btn-setharga-delete" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-delete"></i>
                        </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master.set_harga.set_harga', compact('title', 'lokasiList'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Set Harga';

        $barang = Barang::select('id', 'nama', 'kode_barang', 'harga', 'merek')
        ->where('delete', 0)
        ->distinct()
        ->get();

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

        $getNamaBarang = Barang::where('merek', $request->merek)->first();

        $harga = str_replace('.', '', $request->harga);
        $untung = str_replace('.', '', $request->untung);
        $harga_jual = str_replace('.', '', $request->harga_jual);
        $status = $request->status === 'on' ? 'Aktif' : 'Tidak Aktif';

        $existingSetHargas = SetHarga::where('kode_barang', $request->kode_barang)
            ->where('merek', $request->merek)
            ->where('delete', 0)
            ->get();

        foreach ($existingSetHargas as $existingSetHarga) {
            $existingSetHarga->update(['status' => 'Tidak Aktif']);
        }


        SetHarga::create([
            'id_lokasi' => auth()->user()->id_lokasi,
            'id_barang' => $getNamaBarang->id,
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
        $barang = Barang::select('id', 'nama', 'kode_barang', 'harga', 'merek')
        ->where('delete', 0)
        ->distinct()
        ->get();

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


        $getNamaBarang = Barang::where('merek', $request->merek)->first();

        $harga = str_replace('.', '', $request->harga);
        $untung = str_replace('.', '', $request->untung);
        $harga_jual = str_replace('.', '', $request->harga_jual);


        $validatedData['nama_barang'] = $getNamaBarang->nama;

        $status = 'Tidak Aktif';
        if($request->status === 'on'){
            $status = 'Aktif';
        }

        $validatedData['status'] = $status;

        $setharga = SetHarga::findOrFail($id);

        $existingActiveSetHarga = SetHarga::where('kode_barang', $setharga->kode_barang)
            ->where('merek', $request->merek)
            ->where('status', 'Aktif')
            ->where('id', '!=', $id)
            ->first();

        if ($existingActiveSetHarga) {
            $existingActiveSetHarga->update([
                'status' => 'Tidak Aktif',
                'last_user' => auth()->id()
            ]);
        }

        $setharga->update([
            'id_lokasi' => auth()->user()->id_lokasi,
            'id_barang' => $getNamaBarang->id,
            'nama_barang' => $getNamaBarang->nama,
            'kode_barang' => $request->kode_barang,
            'merek' => $request->merek,
            'harga' => $harga,
            'untung' => $untung,
            'harga_jual' => $harga_jual,
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

        $setharga->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Alert::success('Data Set Harga berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);

        // return redirect('/setharga');
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
        $filePath = public_path('import_tamplate/template_setharga.csv');
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

            return response()->json(['code' => 200, 'success' => 'Data berhasil diimport!']);

        } catch (\Exception $e) {

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->errors()['import_errors'] ?? ['Terjadi kesalahan yang tidak diketahui.'];
                return response()->json(['code' => 400, 'error' => $errors]);
            }

            return response()->json(['code' => 400, 'error' => 'Terjadi kesalahan saat memproses file.']);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $setharga = SetHarga::findOrFail($id);

        $existingActiveSetHarga = SetHarga::where('kode_barang', $setharga->kode_barang)
            ->where('merek', $setharga->merek)
            ->where('status', 'Aktif')
            ->where('id', '!=', $id)
            ->first();

        if ($existingActiveSetHarga) {
            $existingActiveSetHarga->update([
                'status' => 'Tidak Aktif',
                'last_user' => auth()->id()
            ]);
        }

        $setharga->update([
            'status' => $request['status'],
            'last_user' => auth()->id()
        ]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }



}
