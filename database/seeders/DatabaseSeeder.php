<?php

namespace Database\Seeders;

use App\Models\JuriLomba;
use App\Models\KriteriaPenilaian;
use App\Models\Lomba;
use App\Models\Penilaian;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@lentera.test'],
            [
                'name' => 'Panitia LENTERA',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Juri Users
        $juri1 = User::firstOrCreate(
            ['email' => 'juri1@lentera.test'],
            [
                'name' => 'Dra. Hj. Siti Rohmah, M.Pd (Juri 1)',
                'password' => Hash::make('password'),
                'role' => 'juri',
                'email_verified_at' => now(),
            ]
        );

        $juri2 = User::firstOrCreate(
            ['email' => 'juri2@lentera.test'],
            [
                'name' => 'Ahmad Hidayat, S.S (Juri 2)',
                'password' => Hash::make('password'),
                'role' => 'juri',
                'email_verified_at' => now(),
            ]
        );

        $juri3 = User::firstOrCreate(
            ['email' => 'juri3@lentera.test'],
            [
                'name' => 'Dewi Anggraeni, S.Sn (Juri 3)',
                'password' => Hash::make('password'),
                'role' => 'juri',
                'email_verified_at' => now(),
            ]
        );

        // 3. Competitions data
        $lombaData = [
            [
                'nama' => 'Lomba Baca Puisi',
                'slug' => 'lomba-baca-puisi',
                'deskripsi' => 'Lomba pembacaan puisi bertema Semangat Kebangsaan & Bulan Bahasa.',
                'status' => 'aktif',
                'kriteria' => [
                    ['nama_kriteria' => 'Penghayatan & Ekspresi', 'bobot' => 30, 'skala_max' => 100],
                    ['nama_kriteria' => 'Artikulasi & Diksi Vokal', 'bobot' => 30, 'skala_max' => 100],
                    ['nama_kriteria' => 'Intonasi & Tempo', 'bobot' => 20, 'skala_max' => 100],
                    ['nama_kriteria' => 'Gestur & Penampilan', 'bobot' => 20, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Aditya Pratama', 'asal_kelas' => 'X-A'],
                    ['nomor_urut' => '02', 'nama' => 'Nabila Putri Cahyani', 'asal_kelas' => 'XI-MIPA 1'],
                    ['nomor_urut' => '03', 'nama' => 'Rizky Ramadhan', 'asal_kelas' => 'XI-IPS 2'],
                    ['nomor_urut' => '04', 'nama' => 'Siti Maulida', 'asal_kelas' => 'XII-MIPA 3'],
                    ['nomor_urut' => '05', 'nama' => 'Dimas Arya Wijaya', 'asal_kelas' => 'X-E'],
                ],
                'juris' => [$juri1->id, $juri2->id],
            ],
            [
                'nama' => 'Lomba Pidato Bahasa Indonesia',
                'slug' => 'lomba-pidato-bahasa-indonesia',
                'deskripsi' => 'Lomba pidato bertema Peran Generasi Muda dalam Melestarikan Bahasa Persatuan.',
                'status' => 'aktif',
                'kriteria' => [
                    ['nama_kriteria' => 'Kesesuaian Tema & Bobot Isi', 'bobot' => 35, 'skala_max' => 100],
                    ['nama_kriteria' => 'Retorika & Sistematika Penyampaian', 'bobot' => 25, 'skala_max' => 100],
                    ['nama_kriteria' => 'Tata Bahasa & Ragam Kata', 'bobot' => 25, 'skala_max' => 100],
                    ['nama_kriteria' => 'Ketepatan Alokasi Waktu', 'bobot' => 15, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Farhan Al Ghifari', 'asal_kelas' => 'XI-MIPA 2'],
                    ['nomor_urut' => '02', 'nama' => 'Aulia Zahra Salsabila', 'asal_kelas' => 'X-B'],
                    ['nomor_urut' => '03', 'nama' => 'Bintang Kusuma', 'asal_kelas' => 'XII-IPS 1'],
                ],
                'juris' => [$juri1->id],
            ],
            [
                'nama' => 'Lomba Kaligrafi Aksara Sunda',
                'slug' => 'lomba-kaligrafi-aksara-sunda',
                'deskripsi' => 'Lomba penulisan aksara Sunda baku dengan kaidah estetika kaligrafi khas Pasundan.',
                'status' => 'aktif',
                'kriteria' => [
                    ['nama_kriteria' => 'Ketepatan Kaidah Aksara Sunda', 'bobot' => 40, 'skala_max' => 100],
                    ['nama_kriteria' => 'Kerapian & Kebersihan Karya', 'bobot' => 30, 'skala_max' => 100],
                    ['nama_kriteria' => 'Kreativitas & Komposisi Visual', 'bobot' => 30, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Galih Rangga', 'asal_kelas' => 'X-C'],
                    ['nomor_urut' => '02', 'nama' => 'Rina Sugianti', 'asal_kelas' => 'XI-Bahasa'],
                ],
                'juris' => [$juri2->id],
            ],
            [
                'nama' => 'Lomba Tari Daerah Nusantara',
                'slug' => 'lomba-tari-daerah-nusantara',
                'deskripsi' => 'Lomba tari tradisional Nusantara kategori tunggal maupun berpasangan.',
                'status' => 'aktif',
                'kriteria' => [
                    ['nama_kriteria' => 'Wiraga (Ketepatan Gerak & Teknik)', 'bobot' => 35, 'skala_max' => 100],
                    ['nama_kriteria' => 'Wirama (Kesesuaian Irama & Tempo)', 'bobot' => 35, 'skala_max' => 100],
                    ['nama_kriteria' => 'Wirasa (Penjiwaan Karakter & Ekspresi)', 'bobot' => 30, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Tri Wulandari', 'asal_kelas' => 'XI-MIPA 4'],
                    ['nomor_urut' => '02', 'nama' => 'Sanggar Tari Sasando', 'asal_kelas' => 'X-D'],
                ],
                'juris' => [$juri3->id],
            ],
            [
                'nama' => 'Lomba English Story Telling',
                'slug' => 'lomba-english-story-telling',
                'deskripsi' => 'Folklore and narrative story telling competition in English.',
                'status' => 'aktif',
                'kriteria' => [
                    ['nama_kriteria' => 'Pronunciation & Fluency', 'bobot' => 35, 'skala_max' => 100],
                    ['nama_kriteria' => 'Story Mastery & Dramatic Appeal', 'bobot' => 30, 'skala_max' => 100],
                    ['nama_kriteria' => 'Stage Presence & Costume/Props', 'bobot' => 20, 'skala_max' => 100],
                    ['nama_kriteria' => 'Pacing & Time Limit', 'bobot' => 15, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Jessica Tanuwijaya', 'asal_kelas' => 'XII-MIPA 1'],
                    ['nomor_urut' => '02', 'nama' => 'Arkananta Putra', 'asal_kelas' => 'X-A'],
                ],
                'juris' => [$juri1->id, $juri2->id],
            ],
            [
                'nama' => 'Lomba Modern Dance Cover',
                'slug' => 'lomba-modern-dance-cover',
                'deskripsi' => 'Kompetisi dance cover modern & K-Pop grup tingkat sekolah.',
                'status' => 'draft',
                'kriteria' => [
                    ['nama_kriteria' => 'Koreografi & Sinkronisasi', 'bobot' => 40, 'skala_max' => 100],
                    ['nama_kriteria' => 'Power, Ketukan & Musikalitas', 'bobot' => 30, 'skala_max' => 100],
                    ['nama_kriteria' => 'Ekspresi & Visual Konsep', 'bobot' => 30, 'skala_max' => 100],
                ],
                'peserta' => [
                    ['nomor_urut' => '01', 'nama' => 'Velocity Dance Crew', 'asal_kelas' => 'Campuran X-XI'],
                ],
                'juris' => [$juri3->id],
            ],
        ];

        foreach ($lombaData as $data) {
            $lomba = Lomba::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'nama' => $data['nama'],
                    'deskripsi' => $data['deskripsi'],
                    'status' => $data['status'],
                ]
            );

            // Kriteria
            foreach ($data['kriteria'] as $k) {
                KriteriaPenilaian::updateOrCreate(
                    [
                        'lomba_id' => $lomba->id,
                        'nama_kriteria' => $k['nama_kriteria'],
                    ],
                    [
                        'bobot' => $k['bobot'],
                        'skala_max' => $k['skala_max'],
                    ]
                );
            }

            // Peserta
            foreach ($data['peserta'] as $p) {
                Peserta::updateOrCreate(
                    [
                        'lomba_id' => $lomba->id,
                        'nomor_urut' => $p['nomor_urut'],
                    ],
                    [
                        'nama' => $p['nama'],
                        'asal_kelas' => $p['asal_kelas'],
                    ]
                );
            }

            // Assign Juri
            foreach ($data['juris'] as $jId) {
                JuriLomba::firstOrCreate([
                    'lomba_id' => $lomba->id,
                    'user_id' => $jId,
                ]);
            }
        }

        // Seed some sample scores for Lomba Puisi so leaderboard has live data
        $puisi = Lomba::where('slug', 'lomba-baca-puisi')->first();
        if ($puisi) {
            $juriLomba1 = JuriLomba::where('lomba_id', $puisi->id)->where('user_id', $juri1->id)->first();
            $juriLomba2 = JuriLomba::where('lomba_id', $puisi->id)->where('user_id', $juri2->id)->first();
            $pesertaList = $puisi->peserta()->get();
            $kriteriaList = $puisi->kriteria()->get();

            // Score first 2 participants by Juri 1
            if ($juriLomba1 && $pesertaList->count() >= 2) {
                $p1 = $pesertaList[0];
                $scores1 = [88, 85, 90, 84];
                foreach ($kriteriaList as $idx => $crit) {
                    Penilaian::updateOrCreate(
                        ['juri_lomba_id' => $juriLomba1->id, 'peserta_id' => $p1->id, 'kriteria_id' => $crit->id],
                        ['skor' => $scores1[$idx] ?? 85]
                    );
                }

                $p2 = $pesertaList[1];
                $scores2 = [92, 90, 88, 91];
                foreach ($kriteriaList as $idx => $crit) {
                    Penilaian::updateOrCreate(
                        ['juri_lomba_id' => $juriLomba1->id, 'peserta_id' => $p2->id, 'kriteria_id' => $crit->id],
                        ['skor' => $scores2[$idx] ?? 90]
                    );
                }
            }

            // Score first participant by Juri 2
            if ($juriLomba2 && $pesertaList->count() >= 1) {
                $p1 = $pesertaList[0];
                $scores1 = [86, 88, 89, 87];
                foreach ($kriteriaList as $idx => $crit) {
                    Penilaian::updateOrCreate(
                        ['juri_lomba_id' => $juriLomba2->id, 'peserta_id' => $p1->id, 'kriteria_id' => $crit->id],
                        ['skor' => $scores1[$idx] ?? 87]
                    );
                }
            }
        }
    }
}
