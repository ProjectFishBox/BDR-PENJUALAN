<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Log;




use App\Models\Lokasi;




class LokasiControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $title = 'Lokasi';

        $cacheKey = 'lokasi_data';
        $cacheDuration = now()->addMinutes(3);

        $search = $request->get('search');

        if ($request->ajax()) {

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return Lokasi::all()->where('delete', 0);
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                            <button class="btn btn-icon btn-primary btn-lokasi-edit" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-danger btn-lokasi-delete" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-delete"></i>
                            </button>
                            ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master.lokasi.lokasi', compact('title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Lokasi';

        $formRoute = 'store-lokasi';

        return view('pages.master.lokasi.add_lokasi', compact('title', 'formRoute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $validateData['create_by'] = auth()->id();
        $validateData['last_user'] = auth()->id();

        Lokasi::create($validateData);
        Cache::forget('lokasi_data');

        Alert::success('Berhasil Menambahkan data lokasi.');
        return redirect('/lokasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $title = 'Edit Lokasi';

        $lokasi = Lokasi::findOrFail($id);

        return view('pages.master.lokasi.edit_lokasi', compact('lokasi', 'title'));
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $lokasi = Lokasi::findOrFail($id);
        Cache::forget('lokasi_data');

        $lokasi->update([
            'nama' => $request->nama,
            'last_user' => auth()->id(),
        ]);

        Alert::success('Berhasil Merubah data lokasi.');

        return redirect('/lokasi');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $lokasi = Lokasi::findOrFail($id);

        // dd($lokasi);

            $lokasi->update([
                'delete' => 1,
                'last_user' => auth()->id()
            ]);

            // Cek hasil update
            // dd($updated);

            Cache::forget('lokasi_data');

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
