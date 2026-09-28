<?php

namespace App\Http\Controllers\Juri;

use App\Http\Controllers\Controller;
use App\Models\JuriLomba;
use App\Models\Lomba;
use App\Models\Penilaian;
use App\Models\Peserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PenilaianController extends Controller
{
    public function form(Request $request, Lomba $lomba): Response|RedirectResponse
    {
        $user = $request->user();

        $juriLomba = JuriLomba::where('lomba_id', $lomba->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $juriLomba) {
            abort(403, 'Anda tidak ditugaskan sebagai juri pada lomba ini.');
        }

        $lomba->load(['kriteria', 'peserta']);

        // Fetch all current scores from this juri for this competition
        $existingPenilaian = Penilaian::where('juri_lomba_id', $juriLomba->id)->get();

        $kriteriaList = $lomba->kriteria;
        $totalBobot = $kriteriaList->sum('bobot') ?: 1;

        $pesertaData = $lomba->peserta->map(function ($peserta) use ($existingPenilaian, $kriteriaList, $totalBobot) {
            $pesertaScores = $existingPenilaian->where('peserta_id', $peserta->id);
            $scoresMap = [];
            $totalScore = 0;
            $hasAllScores = true;

            foreach ($kriteriaList as $krit) {
                $scoreItem = $pesertaScores->firstWhere('kriteria_id', $krit->id);
                $val = $scoreItem ? (float) $scoreItem->skor : null;
                $scoresMap[$krit->id] = $val;

                if ($val !== null) {
                    $totalScore += ($val * (float) $krit->bobot) / (float) $totalBobot;
                } else {
                    $hasAllScores = false;
                }
            }

            return [
                'id' => $peserta->id,
                'nomor_urut' => $peserta->nomor_urut,
                'nama' => $peserta->nama,
                'asal_kelas' => $peserta->asal_kelas,
                'scores' => $scoresMap,
                'total_score' => round($totalScore, 2),
                'is_rated' => $hasAllScores && $kriteriaList->count() > 0,
            ];
        });

        // Determine currently active participant id from query parameter or default to first unrated
        $requestedPesertaId = $request->query('peserta_id');
        $activePeserta = null;

        if ($requestedPesertaId) {
            $activePeserta = $pesertaData->firstWhere('id', (int) $requestedPesertaId);
        }

        if (! $activePeserta) {
            // Find first unrated
            $activePeserta = $pesertaData->firstWhere('is_rated', false) ?? $pesertaData->first();
        }

        return Inertia::render('Juri/PenilaianForm', [
            'lomba' => [
                'id' => $lomba->id,
                'nama' => $lomba->nama,
                'slug' => $lomba->slug,
                'deskripsi' => $lomba->deskripsi,
                'status' => $lomba->status,
                'is_locked' => $lomba->isLocked(),
            ],
            'kriteria' => $kriteriaList,
            'pesertaList' => $pesertaData,
            'activePesertaId' => $activePeserta ? $activePeserta['id'] : null,
        ]);
    }

    public function store(Request $request, Lomba $lomba, Peserta $peserta): RedirectResponse
    {
        $user = $request->user();

        $juriLomba = JuriLomba::where('lomba_id', $lomba->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $juriLomba) {
            abort(403, 'Anda tidak ditugaskan sebagai juri pada lomba ini.');
        }

        if ($lomba->isLocked()) {
            return back()->with('error', 'Lomba ini telah dikunci oleh panitia. Skor tidak dapat diubah lagi.');
        }

        // Validate scores
        $kriteriaList = $lomba->kriteria()->get();
        $rules = [];
        foreach ($kriteriaList as $krit) {
            $rules["scores.{$krit->id}"] = "required|numeric|min:0|max:{$krit->skala_max}";
        }

        $validated = $request->validate($rules, [
            'scores.*.required' => 'Semua kriteria wajib diisi nilainya.',
            'scores.*.numeric' => 'Nilai harus berupa angka.',
            'scores.*.min' => 'Nilai minimal adalah 0.',
            'scores.*.max' => 'Nilai melebihi skala maksimum.',
        ]);

        foreach ($kriteriaList as $krit) {
            $skorValue = $validated['scores'][$krit->id];

            Penilaian::updateOrCreate(
                [
                    'juri_lomba_id' => $juriLomba->id,
                    'peserta_id' => $peserta->id,
                    'kriteria_id' => $krit->id,
                ],
                [
                    'skor' => $skorValue,
                ]
            );
        }

        // Determine next participant to rate
        $nextPeserta = $lomba->peserta()
            ->where('id', '!=', $peserta->id)
            ->where('nomor_urut', '>', $peserta->nomor_urut)
            ->first() ?? $lomba->peserta()->where('id', '!=', $peserta->id)->first();

        $redirectQuery = $nextPeserta ? ['peserta_id' => $nextPeserta->id] : [];

        return redirect()->route('juri.penilaian.form', array_merge(['lomba' => $lomba->id], $redirectQuery))
            ->with('success', "Nilai untuk {$peserta->nama} ({$peserta->nomor_urut}) berhasil disimpan!");
    }
}
