<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KriteriaPenilaian;
use App\Models\Lomba;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KriteriaController extends Controller
{
    public function index(Lomba $lomba): Response
    {
        $lomba->load('kriteria');

        return Inertia::render('Admin/Lomba/Kriteria', [
            'lomba' => $lomba,
            'kriteria' => $lomba->kriteria,
            'totalBobot' => $lomba->kriteria->sum('bobot'),
        ]);
    }

    public function store(Request $request, Lomba $lomba): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0.1',
            'skala_max' => 'required|integer|min:10|max:1000',
        ]);

        $lomba->kriteria()->create($validated);

        return back()->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, Lomba $lomba, KriteriaPenilaian $kriteria): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0.1',
            'skala_max' => 'required|integer|min:10|max:1000',
        ]);

        $kriteria->update($validated);

        return back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    public function destroy(Lomba $lomba, KriteriaPenilaian $kriteria): RedirectResponse
    {
        $kriteria->delete();

        return back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
