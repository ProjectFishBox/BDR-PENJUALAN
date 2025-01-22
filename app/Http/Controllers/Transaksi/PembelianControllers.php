<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class PembelianControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'List Pembelian';

        $pembelian = Pembelian::all();
        $search = trim($request->get('search'));


        $data = Pembelian::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('no_nota', 'like', "%$search%")
                ->orWhere('kontainer', 'like', "%$search%");
            });
        })
        ->with('lokasi')
        ->get();

        return view('pages.pembelian.pembelian', compact('title','pembelian', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
