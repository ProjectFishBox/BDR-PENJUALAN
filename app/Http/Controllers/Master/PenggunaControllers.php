<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use App\Models\User;
use App\Models\Lokasi;


class PenggunaControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Pengguna';

        $cacheKey = 'pengguna_data';
        $cacheDuration = now()->addMinutes(3);

        if ($request->ajax()) {

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return User::with('lokasi')->where('delete', 0)->get();
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                            <button class="btn btn-icon btn-primary btn-pengguna-edit" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-danger btn-pengguna-delete" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-delete"></i>
                            </button>
                            ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master.pengguna.pengguna', compact('title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pengguna';

        $lokasi = Lokasi::all();

        return view('pages.master.pengguna.tambah_pengguna', compact('title', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'password' => 'required|min:3',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'jabatan' => 'nullable'
        ]);


        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        User::create($validatedData);
        Cache::forget('pengguna_data');

        Alert::success('Berhasil Menambahkan data Pengguna.');

        return redirect('/pengguna');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Pengguna';

        $pengguna = User::findOrFail($id);
        $lokasi = Lokasi::all();


        return view('pages.master.pengguna.edit_pengguna', compact('pengguna', 'title', 'lokasi'));
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
            'nama' => 'required|max:25',
            'username' => 'required|max:25',
            'id_lokasi' => 'required|integer',
            'id_akses' => 'required|integer',
            'password' => 'nullable|min:3'
        ]);

        $validatedData['last_user'] = auth()->id();

        $user = User::findOrFail($id);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'id_lokasi' => $request->id_lokasi,
            'id_akses' => $request->id_akses,
            'password' => isset($validatedData['password']) ? $validatedData['password'] : $user->password,
            'last_user' => auth()->id(),
        ]);

        Cache::forget('pengguna_data');

        Alert::success('Berhasil Merubah data Pengguna.');

        return redirect('/pengguna');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $pengguna = User::findOrFail($id);

            $pengguna->update([
                'delete' => 1,
                'last_user' => auth()->id()
            ]);

            Cache::forget('pengguna_data');

            Alert::success('Data Pengguna berhasil dihapus.');

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }
}
