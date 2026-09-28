<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LombaController extends Controller
{
    public function index(): Response
    {
        $lomba = Lomba::with(['kriteria', 'peserta', 'juri'])
            ->withCount(['kriteria', 'peserta', 'juri'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Lomba/Index', [
            'lomba' => $lomba,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:draft,aktif,dikunci',
            'kriteria' => 'nullable|array',
            'kriteria.*.nama_kriteria' => 'required|string|max:255',
            'kriteria.*.bobot' => 'required|numeric|min:0.1',
            'kriteria.*.skala_max' => 'required|integer|min:1',
        ]);

        $lomba = Lomba::create([
            'nama' => $validated['nama'],
            'slug' => Str::slug($validated['nama']).'-'.Str::random(4),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        if (! empty($validated['kriteria'])) {
            foreach ($validated['kriteria'] as $crit) {
                $lomba->kriteria()->create([
                    'nama_kriteria' => $crit['nama_kriteria'],
                    'bobot' => $crit['bobot'],
                    'skala_max' => $crit['skala_max'] ?? 100,
                ]);
            }
        }

        return redirect()->route('admin.lomba.index')->with('success', 'Lomba berhasil ditambahkan.');
    }

    public function update(Request $request, Lomba $lomba): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:draft,aktif,dikunci',
        ]);

        $lomba->update($validated);

        return back()->with('success', 'Lomba berhasil diperbarui.');
    }

    public function destroy(Lomba $lomba): RedirectResponse
    {
        $lomba->delete();

        return redirect()->route('admin.lomba.index')->with('success', 'Lomba berhasil dihapus.');
    }

    public function lock(Lomba $lomba): RedirectResponse
    {
        $lomba->update([
            'status' => 'dikunci',
            'locked_at' => now(),
        ]);

        return back()->with('success', "Penilaian lomba '{$lomba->nama}' berhasil dikunci. Juri tidak dapat lagi mengubah skor.");
    }

    public function unlock(Lomba $lomba): RedirectResponse
    {
        $lomba->update([
            'status' => 'aktif',
            'locked_at' => null,
        ]);

        return back()->with('success', "Penilaian lomba '{$lomba->nama}' dibuka kembali.");
    }
}
