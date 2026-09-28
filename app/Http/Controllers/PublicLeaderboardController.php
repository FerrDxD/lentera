<?php

namespace App\Http\Controllers;

use App\Models\Lomba;
use App\Services\ScoringService;
use Inertia\Inertia;
use Inertia\Response;

class PublicLeaderboardController extends Controller
{
    public function index(ScoringService $scoringService): Response
    {
        $lombaList = Lomba::with(['kriteria', 'peserta', 'juri'])
            ->whereIn('status', ['aktif', 'dikunci'])
            ->orderBy('id', 'asc')
            ->get();

        $competitionsWithLeaderboards = $lombaList->map(function ($lomba) use ($scoringService) {
            return [
                'id' => $lomba->id,
                'nama' => $lomba->nama,
                'slug' => $lomba->slug,
                'deskripsi' => $lomba->deskripsi,
                'status' => $lomba->status,
                'locked_at' => $lomba->locked_at ? $lomba->locked_at->toIso8601String() : null,
                'kriteria' => $lomba->kriteria,
                'leaderboard' => $scoringService->getLeaderboard($lomba),
            ];
        });

        return Inertia::render('Welcome', [
            'competitions' => $competitionsWithLeaderboards,
        ]);
    }
}
