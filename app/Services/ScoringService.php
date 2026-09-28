<?php

namespace App\Services;

use App\Models\Lomba;
use App\Models\Peserta;

class ScoringService
{
    /**
     * Compute leaderboard for a given competition.
     */
    public function getLeaderboard(Lomba $lomba): array
    {
        $kriteria = $lomba->kriteria()->get();
        $pesertaList = $lomba->peserta()->get();
        $juriLombaList = $lomba->juriLomba()->with('user')->get();

        $totalBobot = $kriteria->sum('bobot');
        if ($totalBobot <= 0) {
            $totalBobot = 1;
        }

        $results = [];

        foreach ($pesertaList as $peserta) {
            $penilaianList = $peserta->penilaian()->with('juriLomba.user')->get();

            $kriteriaAverages = [];
            $totalFinalScore = 0;
            $juriScores = [];

            // Group by criterion
            foreach ($kriteria as $krit) {
                $scoresForKrit = $penilaianList->where('kriteria_id', $krit->id);
                $count = $scoresForKrit->count();
                $avg = $count > 0 ? (float) $scoresForKrit->avg('skor') : 0.0;
                $weighted = ($avg * (float) $krit->bobot) / (float) $totalBobot;

                $kriteriaAverages[$krit->id] = [
                    'kriteria_id' => $krit->id,
                    'nama_kriteria' => $krit->nama_kriteria,
                    'bobot' => $krit->bobot,
                    'skala_max' => $krit->skala_max,
                    'avg_skor' => round($avg, 2),
                    'weighted_skor' => round($weighted, 2),
                    'sample_count' => $count,
                ];

                $totalFinalScore += $weighted;
            }

            // Group by juri
            foreach ($juriLombaList as $jl) {
                $juriScoresForPeserta = $penilaianList->where('juri_lomba_id', $jl->id);
                $isComplete = $juriScoresForPeserta->count() >= $kriteria->count() && $kriteria->count() > 0;
                
                $juriWeighted = 0;
                $criteriaScores = [];
                foreach ($kriteria as $krit) {
                    $item = $juriScoresForPeserta->firstWhere('kriteria_id', $krit->id);
                    $val = $item ? (float) $item->skor : null;
                    $criteriaScores[$krit->id] = $val;
                    if ($val !== null) {
                        $juriWeighted += ($val * (float) $krit->bobot) / (float) $totalBobot;
                    }
                }

                $juriScores[$jl->id] = [
                    'juri_id' => $jl->user_id,
                    'juri_name' => $jl->user->name,
                    'is_complete' => $isComplete,
                    'total_skor' => round($juriWeighted, 2),
                    'scores' => $criteriaScores,
                ];
            }

            $totalJudges = $juriLombaList->count();
            $judgesSubmitted = count(array_filter($juriScores, fn ($j) => $j['is_complete']));

            $results[] = [
                'peserta_id' => $peserta->id,
                'nomor_urut' => $peserta->nomor_urut,
                'nama' => $peserta->nama,
                'asal_kelas' => $peserta->asal_kelas,
                'kriteria_breakdown' => $kriteriaAverages,
                'juri_breakdown' => $juriScores,
                'judges_submitted' => $judgesSubmitted,
                'total_judges' => $totalJudges,
                'total_skor' => round($totalFinalScore, 2),
            ];
        }

        // Sort descending by total score, then by nomor urut
        usort($results, function ($a, $b) {
            if ($b['total_skor'] != $a['total_skor']) {
                return $b['total_skor'] <=> $a['total_skor'];
            }
            return $a['nomor_urut'] <=> $b['nomor_urut'];
        });

        // Assign rank
        $currentRank = 1;
        foreach ($results as $index => &$res) {
            $res['rank'] = $currentRank++;
        }

        return $results;
    }
}
