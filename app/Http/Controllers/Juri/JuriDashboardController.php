<?php

namespace App\Http\Controllers\Juri;

use App\Http\Controllers\Controller;
use App\Models\JuriLomba;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JuriDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $juriLombas = JuriLomba::where('user_id', $user->id)
            ->with(['lomba.kriteria', 'lomba.peserta'])
            ->get();

        $assignedLomba = $juriLombas->map(function ($jl) {
            $lomba = $jl->lomba;
            $pesertaCount = $lomba->peserta->count();
            $kriteriaCount = $lomba->kriteria->count();

            // Check how many participants have scores submitted by this juri
            $ratedPesertaCount = 0;
            if ($kriteriaCount > 0 && $pesertaCount > 0) {
                $ratedPesertaCount = Penilaian::where('juri_lomba_id', $jl->id)
                    ->selectRaw('peserta_id, count(id) as c')
                    ->groupBy('peserta_id')
                    ->havingRaw('count(id) >= ?', [$kriteriaCount])
                    ->get()
                    ->count();
            }

            return [
                'id' => $lomba->id,
                'nama' => $lomba->nama,
                'slug' => $lomba->slug,
                'deskripsi' => $lomba->deskripsi,
                'status' => $lomba->status,
                'locked_at' => $lomba->locked_at ? $lomba->locked_at->toIso8601String() : null,
                'peserta_count' => $pesertaCount,
                'kriteria_count' => $kriteriaCount,
                'rated_count' => $ratedPesertaCount,
                'is_all_rated' => $pesertaCount > 0 && $ratedPesertaCount >= $pesertaCount,
            ];
        });

        return Inertia::render('Juri/Dashboard', [
            'assignedLomba' => $assignedLomba,
        ]);
    }
}
