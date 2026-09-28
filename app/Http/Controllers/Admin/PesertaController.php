<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use App\Models\Peserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PesertaController extends Controller
{
    public function index(Lomba $lomba): Response
    {
        $peserta = $lomba->peserta()->withCount('penilaian')->get();

        return Inertia::render('Admin/Lomba/Peserta', [
            'lomba' => $lomba,
            'peserta' => $peserta,
        ]);
    }

    public function store(Request $request, Lomba $lomba): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'asal_kelas' => 'required|string|max:100',
        ]);

        $lomba->peserta()->create($validated);

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function update(Request $request, Lomba $lomba, Peserta $peserta): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'asal_kelas' => 'required|string|max:100',
        ]);

        $peserta->update($validated);

        return back()->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(Lomba $lomba, Peserta $peserta): RedirectResponse
    {
        $peserta->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
