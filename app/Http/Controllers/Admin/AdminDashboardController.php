<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        $lombaList = Lomba::with(['kriteria', 'peserta', 'juri'])
            ->withCount(['kriteria', 'peserta', 'juri'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($lomba) {
                // Calculate scoring progress
                $totalPossiblePenilaian = $lomba->peserta_count * $lomba->juri_count;
                $completedPenilaian = 0;

                if ($totalPossiblePenilaian > 0) {
                    $juriLombaIds = $lomba->juriLomba()->pluck('id');
                    $kriteriaCount = $lomba->kriteria_count;

                    if ($kriteriaCount > 0) {
                        // Count how many (juri_lomba_id, peserta_id) pairs have all criteria graded
                        $completedPenilaian = \App\Models\Penilaian::whereIn('juri_lomba_id', $juriLombaIds)
                            ->selectRaw('juri_lomba_id, peserta_id, count(id) as c')
                            ->groupBy('juri_lomba_id', 'peserta_id')
                            ->havingRaw('count(id) >= ?', [$kriteriaCount])
                            ->get()
                            ->count();
                    }
                }

                $progressPercent = $totalPossiblePenilaian > 0
                    ? round(($completedPenilaian / $totalPossiblePenilaian) * 100)
                    : 0;

                return [
                    'id' => $lomba->id,
                    'nama' => $lomba->nama,
                    'slug' => $lomba->slug,
                    'status' => $lomba->status,
                    'locked_at' => $lomba->locked_at ? $lomba->locked_at->toIso8601String() : null,
                    'kriteria_count' => $lomba->kriteria_count,
                    'peserta_count' => $lomba->peserta_count,
                    'juri_count' => $lomba->juri_count,
                    'completed_penilaian' => $completedPenilaian,
                    'total_possible_penilaian' => $totalPossiblePenilaian,
                    'progress_percent' => $progressPercent,
                ];
            });

        $stats = [
            'total_lomba' => $lombaList->count(),
            'lomba_aktif' => $lombaList->where('status', 'aktif')->count(),
            'lomba_dikunci' => $lombaList->where('status', 'dikunci')->count(),
            'total_peserta' => Peserta::count(),
            'total_juri' => User::where('role', 'juri')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'lombaList' => $lombaList,
        ]);
    }
}
