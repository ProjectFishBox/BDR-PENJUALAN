<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Gabungkan;
use App\Models\GabungkanDetail;

class GabungkanControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Gabungkan';

        $cacheKey = 'gabungkan_data';
        $cacheDuration = now()->addMinutes(3);

        if ($request->ajax()) {

            $data = Cache::remember($cacheKey, $cacheDuration, function () {
                return Gabungkan::with('lokasi', 'user')->where('delete', 0)->get();
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                            <button class="btn btn-icon btn-warning btn-gabungkan-print" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-printer"></i>
                            </button>

                            <button class="btn btn-icon btn-primary btn-gabungkan-edit" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-danger btn-gabungkan-delete" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-delete"></i>
                            </button>

                            ';
                    return $btn;
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d-m-Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.transaksi.gabungkan.gabungkan', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Gabungkan';

        return view('pages.transaksi.gabungkan.tambah_gabungkan', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'total_ball' => 'required'
        ]);


        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        // dd('test');

        $gabungkan = Gabungkan::create($validatedData);

        $tableData = $request->input('table_data');
        foreach ($tableData as $data) {

            DB::table('gabungkan_detail')->insert([
                'id_gabungkan' => $gabungkan->id,
                'kode_barang' => $data['kode_barang'],
                'merek' => $data['merek'],
                'jumlah' => $data['jumlah'],
                'create_by' => auth()->id(),
                'last_user' => auth()->id()
            ]);
        }

        Cache::forget('gabungkan_data');

        Alert::success('Berhasil Menambahkan data Gabungkan.');

        return redirect('/gabungkan');

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        if (!$request->ajax()) {
            redirect('/dashboard');
        }

        $title = "Detail Gabungkan";

        $id = $request->input('id');

        $gabungkan = Gabungkan::with('lokasi', 'user')->where('id', $id)->first();

        $detailGabungkan = GabungkanDetail::where('id_gabungkan', $id)->get();

        return view('components.modal.modal_detail_data_gabungkan', compact('title', 'gabungkan', 'detailGabungkan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Gabungkan';

        $gabungkan = Gabungkan::findOrFail($id);

        $detailGabungkan = GabungkanDetail::where('id_gabungkan', $gabungkan->id)->get();

        return view('pages.transaksi.gabungkan.edit_gabungkan', compact('title', 'gabungkan', 'detailGabungkan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'total_ball' => 'required'
        ]);

        $validatedData['id_lokasi'] = auth()->user()->id_lokasi;
        $validatedData['create_by'] = auth()->id();
        $validatedData['last_user'] = auth()->id();

        $gabungkan = Gabungkan::findOrFail($id);

        $gabungkan->update([
            'total_ball'    => $request->total_ball,
            'last_user'  => auth()->user()->id
        ]);

        GabungkanDetail::where('id_gabungkan', $id)->delete();

        $tableData  = $request->input('table_data');

        foreach ($tableData as $key => $item) {

            GabungkanDetail::create([
                'id_gabungkan' => $gabungkan->id,
                'kode_barang' => $item['kode_barang'],
                'merek' => $item['merek'],
                'jumlah' => $item['jumlah'],
                'create_by' => auth()->id(),
                'last_user' => auth()->id()
            ]);
        }

        Cache::forget('gabungkan_data');

        Alert::success('Berhasil Merubah data Gabungkan.');

        return redirect('/gabungkan');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gabungkan = Gabungkan::findOrFail($id);

        $gabungkan->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        GabungkanDetail::where('id_gabungkan', $id)->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Cache::forget('gabungkan_data');

        Alert::success('Data Gabungkan berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function modalImport(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }

        $title = "Import Detail Gabungkan";

        $action = "import-detailGabungkan";

        $type = 'detailGabungkan';

        return view('components.modal.modal_import_data', compact('title', 'action', 'type'));
    }

    public function downloadTamplate()
    {
        $filePath = public_path('import_tamplate/tamplate_gabungkan.csv');
        $fileName = 'tamplate_gabungkan.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $fileName);
    }


    public function printData($id)
    {
        $gabungkan = Gabungkan::findOrFail($id);

        $detailGabungkan = GabungkanDetail::where('id_gabungkan', $id)->get();

        $pdf = Pdf::loadView('components.pdf.gabungkan_pdf', compact('gabungkan', 'detailGabungkan'));

        return $pdf->stream('gabungkan-'.$id.'.pdf');
    }


}
