<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $berita = Berita::latest()->get();


        return view('berita.index', compact('berita'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $berita = Berita::count();

        return view('berita.create', compact('berita'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'judul_berita'      => 'required',
            'isi_berita'        => 'required',
            'gambar_berita'     => 'required|image|mimes:png,jpg,jpeg',
        ]);

        //upload image
        $gambar_berita = $request->file('gambar_berita');
        $gambar_berita->storeAs('public/image', $gambar_berita->hashName());

        $berita = Berita::create([
            'judul_berita'      => $request->judul_berita,
            'isi_berita'        => $request->isi_berita,
            'gambar_berita'     => $gambar_berita->hashName(),
        ]);

        if ($berita) {
            //redirect dengan pesan sukses
            return redirect()->route('berita.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            //redirect dengan pesan error
            return redirect()->route('berita.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Berita $beritum)
    {
        $berita = Berita::count();

        return view('berita.edit', compact('beritum','berita'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Berita $beritum)
    {
        $this->validate($request, [
            'judul_berita'      => 'required',
            'isi_berita'        => 'required',
        ]);

        //get data Berita by ID
        $beritum = Berita::findOrFail($beritum->id);

        if ($request->file('gambar_berita') == "") {

            $beritum->update([
                'judul_berita'      => $request->judul_berita,
                'isi_berita'        => $request->isi_berita,
            ]);
        } else {

            //hapus old image
            Storage::disk('local')->delete('public/image/' . $beritum->gambar_berita);

            //upload new image
            $gambar_berita = $request->file('gambar_berita');
            $gambar_berita->storeAs('public/image', $gambar_berita->hashName());

            $beritum->update([
                'gambar_berita'     => $gambar_berita->hashName(),
                'judul_berita'      => $request->judul_berita,
                'nama_berita'       => $request->nama_berita,
            ]);
        }

        if ($beritum) {
            //redirect dengan pesan sukses
            return redirect()->route('berita.index')->with(['success' => 'Data Berhasil Diupdate!']);
        } else {
            //redirect dengan pesan error
            return redirect()->route('berita.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        Storage::disk('local')->delete('public/image/' . $berita->gambar_berita);
        $berita->delete();

        if ($berita) {
            //redirect dengan pesan sukses
            return redirect()->route('berita.index')->with(['success' => 'Data Berhasil Dihapus!']);
        } else {
            //redirect dengan pesan error
            return redirect()->route('berita.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }
}
