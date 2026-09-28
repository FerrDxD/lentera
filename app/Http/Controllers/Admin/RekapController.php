<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use App\Services\ScoringService;
use Inertia\Inertia;
use Inertia\Response;

class RekapController extends Controller
{
    public function show(Lomba $lomba, ScoringService $scoringService): Response
    {
        $lomba->load(['kriteria', 'peserta', 'juri']);
        $leaderboard = $scoringService->getLeaderboard($lomba);

        return Inertia::render('Admin/Lomba/Rekap', [
            'lomba' => $lomba,
            'kriteria' => $lomba->kriteria,
            'leaderboard' => $leaderboard,
            'totalPeserta' => $lomba->peserta->count(),
            'totalJuri' => $lomba->juri->count(),
        ]);
    }
}
