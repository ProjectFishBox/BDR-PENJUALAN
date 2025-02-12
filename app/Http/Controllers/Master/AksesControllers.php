<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Access_menu;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

use App\Models\Akses;
use App\Models\Menu;

class AksesControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Akses';

        if ($request->ajax()) {

            $data = Akses::with('accessMenus')->where('delete', 0)->orderBy('created_at', 'desc')->get();

            foreach ($data as $aksesItem) {
                $totalMenus = Menu::count();
                $selectedMenus = $aksesItem->accessMenus()->count();

                $aksesItem->all_menus_selected = ($totalMenus == $selectedMenus) ? true : false;
                $aksesItem->akses_menus = $aksesItem->all_menus_selected
                    ? '<li>Semua Menu</li>'
                    : $aksesItem->accessMenus->map(function($menuItem) {
                        return '<li>' . $menuItem->menu->nama . '</li>';
                    })->implode('');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('akses_menu', function ($row) {
                    return $row->akses_menus;
                })
                ->addColumn('action', function ($row) {
                    if ($row->id == 1 && $row->nama == "Super Admin") {
                        return '';
                    }

                    return '
                        <button class="btn btn-icon btn-primary btn-akses-edit" data-id="' . $row->id . '" type="button" role="button">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-danger btn-akses-delete" data-id="' . $row->id . '" type="button" role="button">
                            <i class="anticon anticon-delete"></i>
                        </button>
                    ';
                })
                ->rawColumns(['akses_menu', 'action'])
                ->make(true);
        }

        return view('pages.master.akses.akses', compact('title'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Akses';

        return view('pages.master.akses.tambah_akses', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'nama' => 'required|string|max:255',
            'dashboard' => 'nullable',
            'master_lokasi' => 'nullable',
            'master_akses' => 'nullable',
            'master_pengguna' => 'nullable',
            'master_pelanggan' => 'nullable',
            'master_barang' => 'nullable',
            'master_setharga' => 'nullable',
            'pembelian' => 'nullable',
            'pengeluaran' => 'nullable',
            'penjualan' => 'nullable',
            'gabungkan' => 'nullable',
            'laporan_stok' => 'nullable',
            'laporan_pembelian' => 'nullable',
            'laporan_penjualan' => 'nullable',
            'laporan_pendapatan' => 'nullable',
        ]);


        $akses = Akses::create([
            'nama' => $request->nama,
            'create_by' => auth()->id(),
            'last_user' => auth()->id(),
        ]);

        $menus = Menu::all();

        foreach ($menus as $menu) {
            $menuName = strtolower(str_replace(' ', '_', $menu->nama));
            if ($request->has($menuName) && $request->$menuName) {
                Access_menu::create([
                    'id_akses' => $akses->id,
                    'id_menu' => $menu->id,
                    'create_by' => auth()->id(),
                    'last_user' => auth()->id(),
                ]);
            }
        }

        Alert::success('Berhasil Menambahkan data Akses.');

        return redirect('/akses');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Edit Akses';

        $akses = Akses::with('menus')->findOrFail($id);
        $menus = Menu::all();

        return view('pages.master.akses.edit_akses', compact('akses', 'title', 'menus'));
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

        $nama = $request->input('nama');
        $menus = $request->input('menus', []);

        $validatedMenus = array_keys(array_filter($menus));


        $akses = Akses::findOrFail($id);

        $akses->nama = $nama;
        $akses->last_user = auth()->id();
        $akses->save();

        $menuData = [];
        foreach ($validatedMenus as $menuId) {
            $menuData[$menuId] = [
                'create_by' => auth()->id(),
                'last_user' => auth()->id(),
            ];
        }

        $akses->menus()->sync($menuData);

        Alert::success('Berhasil memperbarui data akses.');

        return redirect('/akses');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $akses = Akses::findOrFail($id);

            $akses->update([
                'delete' => 1,
                'last_user' => auth()->id()
            ]);

            Alert::success('Data Akses berhasil dihapus.');

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }
}
