<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran as ModelsPengeluaran;
use Illuminate\Http\Request;
USE RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;


use App\Models\Pengeluaran;
use App\Models\Lokasi;
use App\Exports\PengeluaranExport;

class PengeluaranControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pengeluaran';

        $lokasiList = Lokasi::all();

        if ($request->ajax()) {
            $lokasiId = $request->query('lokasi');
            $daterange = $request->query('daterange');

            $query = Pengeluaran::with('lokasi')->where('delete', 0)->orderBy('created_at', 'desc');

            if ($lokasiId && $lokasiId !== 'all') {
                $query->where('id_lokasi', $lokasiId);
            }

            if ($daterange) {
                $dates = explode(' - ', $daterange);

                if (count($dates) === 2) {
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            }

            $data = $query->get();


            if ($data->isEmpty()) {
                return response()->json(['data' => []]);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button class="btn btn-icon btn-primary btn-pengeluaran-edit" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-danger btn-pengeluaran-delete" data-id="' . $row->id . '" type="button">
                            <i class="anticon anticon-delete"></i>
                        </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.transaksi.pengeluaran.pengeluaran', compact('title', 'lokasiList'));
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
    public function show(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/dashboard');
        }
        $query = Pengeluaran::query();


        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        $daterange = $request->query('daterange');

        if ($daterange) {
            $dates = explode(' - ', $daterange);

            if (count($dates) === 2) {
                $startDate = date('Y-m-d', strtotime($dates[0]));
                $endDate = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        $pengeluaran = $query->get();


        $title = "Detail Pengeluaran";

        return view('components.modal.modal_detail_data_pengeluaran', compact('title', 'pengeluaran', 'daterange'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Pengeluaran';

        $pengeluaran = Pengeluaran::findOrFail($id);

        return view('pages.transaksi.pengeluaran.edit_pengeluaran', compact('pengeluaran', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->tanggal);


        $validateData = $request->validate([
            'tanggal' => 'required',
            'uraian' => 'required',
            'total' => 'required',
        ]);

        $validateData['total'] = str_replace('.', '', $request->total);

        $validateData['tanggal'] = $request->tanggal;


        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update($validateData);

        Alert::success('Data Pengeluaran berhasil di Update.');

        return redirect('/pengeluaran');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $pengeluaran->update([
            'delete' => 1,
            'last_user' => auth()->id()
        ]);

        Alert::success('Data Pengeluaran berhasil dihapus.');

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function export(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('tanggal', [
                Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                Carbon::createFromFormat('d/m/Y', $request->end)->format('Y-m-d')
            ]);
        }

        if ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }
        if ($request->filled('search')) {
            $query->where('uraian', 'like', '%' . $request->search . '%');
        }

        $pengeluaran = $query->get();

        return Excel::download(new PengeluaranExport($pengeluaran), 'pengeluaran.xlsx');
    }
}
