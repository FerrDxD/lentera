<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, dashboard } from '@/routes';
import { computed, ref } from 'vue';
import { Flame, Trophy, Medal, Award, Lock, Clock, Users, CheckCircle2, ChevronRight } from '@lucide/vue';

const props = defineProps<{
    competitions: {
        id: number;
        nama: string;
        slug: string;
        deskripsi: string | null;
        status: string;
        locked_at: string | null;
        kriteria: { id: number; nama_kriteria: string; bobot: number }[];
        leaderboard: {
            rank: number;
            peserta_id: number;
            nomor_urut: string;
            nama: string;
            asal_kelas: string;
            total_skor: number;
            judges_submitted: number;
            total_judges: number;
        }[];
    }[];
}>();

const activeTab = ref(props.competitions[0]?.id ?? null);
const active = computed(() => props.competitions.find(c => c.id === activeTab.value));
</script>

<template>
    <Head title="LENTERA Score – Festival Bulan Bahasa" />
    <div class="min-h-screen bg-slate-950 text-slate-100 antialiased selection:bg-amber-500 selection:text-slate-950">
        <!-- Top Banner / Header -->
        <header class="sticky top-0 z-30 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md px-6 py-4">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-950 shadow-lg shadow-amber-500/20">
                        <Flame class="h-5 w-5 fill-current" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold tracking-tight text-white text-lg">LENTERA</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Score</span>
                        </div>
                        <p class="text-xs text-slate-400">Festival Bulan Bahasa</p>
                    </div>
                </div>

                <Link
                    :href="$page.props.auth?.user ? dashboard() : login()"
                    class="group inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-lg bg-amber-500 text-slate-950 hover:bg-amber-400 transition-all active:scale-95 shadow-md shadow-amber-500/10"
                >
                    <span>{{ $page.props.auth?.user ? 'Masuk Dashboard' : 'Portal Panitia / Juri' }}</span>
                    <ChevronRight class="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" />
                </Link>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-8 md:py-12 space-y-8">
            <!-- Hero Subtitle -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-slate-900 border border-slate-800 text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Papan Skor Realtime Bulan Bahasa
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white">
                    Klasemen & Hasil Penilaian Lomba
                </h1>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Sistem rekapitulasi penilaian digital resmi untuk seluruh cabang lomba LENTERA. Skor dihitung secara otomatis dan transparan dari para dewan juri.
                </p>
            </div>

            <!-- Empty State -->
            <div v-if="!competitions.length" class="text-center py-20 bg-slate-900/50 rounded-2xl border border-slate-800 max-w-md mx-auto p-8">
                <Flame class="h-12 w-12 text-slate-600 mx-auto mb-3" />
                <h3 class="font-semibold text-white">Belum Ada Lomba yang Aktif</h3>
                <p class="text-xs text-slate-400 mt-1">Silakan tunggu panitia mengaktifkan lomba untuk melihat papan skor.</p>
            </div>

            <div v-else class="space-y-6">
                <!-- Competition Nav Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button
                        v-for="c in competitions"
                        :key="c.id"
                        @click="activeTab = c.id"
                        class="shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all cursor-pointer"
                        :class="activeTab === c.id
                            ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20'
                            : 'bg-slate-900/80 text-slate-300 hover:bg-slate-800 border border-slate-800/80'"
                    >
                        <span>{{ c.nama }}</span>
                        <span
                            v-if="c.status === 'dikunci'"
                            class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                            :class="activeTab === c.id ? 'bg-slate-950/20 text-slate-950' : 'bg-rose-950/80 text-rose-300 border border-rose-800/50'"
                        >
                            Final
                        </span>
                    </button>
                </div>

                <!-- Active Competition Panel -->
                <div v-if="active" class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 md:p-8 space-y-6 shadow-xl backdrop-blur-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl md:text-2xl font-bold text-white tracking-tight">{{ active.nama }}</h2>
                                <span
                                    class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium"
                                    :class="active.status === 'dikunci'
                                        ? 'bg-rose-950/60 text-rose-300 border border-rose-800/60'
                                        : 'bg-emerald-950/60 text-emerald-300 border border-emerald-800/60'"
                                >
                                    <Lock v-if="active.status === 'dikunci'" class="h-3 w-3" />
                                    <Clock v-else class="h-3 w-3" />
                                    <span>{{ active.status === 'dikunci' ? 'Hasil Resmi (Terkunci)' : 'Sedang Berlangsung' }}</span>
                                </span>
                            </div>
                            <p v-if="active.deskripsi" class="text-xs md:text-sm text-slate-400 mt-1 max-w-2xl">{{ active.deskripsi }}</p>
                        </div>

                        <!-- Criteria Pills -->
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="k in active.kriteria"
                                :key="k.id"
                                class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/50 font-medium"
                            >
                                {{ k.nama_kriteria }} ({{ k.bobot }}%)
                            </span>
                        </div>
                    </div>

                    <!-- No participants -->
                    <div v-if="!active.leaderboard.length" class="text-center py-16 text-slate-500">
                        <Users class="h-10 w-10 mx-auto mb-2 text-slate-600" />
                        <p class="text-sm font-medium">Belum ada peserta yang dinilai pada cabang lomba ini.</p>
                    </div>

                    <!-- Leaderboard Table -->
                    <div v-else class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-slate-950/60 text-slate-400 text-xs font-semibold uppercase tracking-wider border-b border-slate-800">
                                    <th class="py-3.5 px-4 w-16 text-center">Peringkat</th>
                                    <th class="py-3.5 px-4">Peserta</th>
                                    <th class="py-3.5 px-4">Asal Kelas</th>
                                    <th class="py-3.5 px-4 text-right">Total Skor Akhir</th>
                                    <th class="py-3.5 px-4 text-right">Status Juri</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <tr
                                    v-for="row in active.leaderboard"
                                    :key="row.peserta_id"
                                    class="transition-colors hover:bg-slate-800/30"
                                    :class="{
                                        'bg-amber-500/[0.04]': row.rank === 1,
                                        'bg-slate-800/10': row.rank === 2,
                                        'bg-amber-900/[0.03]': row.rank === 3,
                                    }"
                                >
                                    <td class="py-3.5 px-4 text-center font-bold">
                                        <div v-if="row.rank === 1" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 font-bold text-xs">
                                            <Trophy class="h-4 w-4" />
                                        </div>
                                        <div v-else-if="row.rank === 2" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-slate-300/20 text-slate-200 border border-slate-400/30 font-bold text-xs">
                                            <Medal class="h-4 w-4" />
                                        </div>
                                        <div v-else-if="row.rank === 3" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-amber-700/20 text-amber-600 border border-amber-600/30 font-bold text-xs">
                                            <Award class="h-4 w-4" />
                                        </div>
                                        <span v-else class="text-slate-500 font-mono text-xs">#{{ row.rank }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-white flex items-center gap-2">
                                            <span class="text-xs font-mono text-slate-500">[{{ row.nomor_urut }}]</span>
                                            <span>{{ row.nama }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400 text-xs">{{ row.asal_kelas }}</td>
                                    <td class="py-3.5 px-4 text-right">
                                        <span class="text-base font-extrabold tabular-nums tracking-tight" :class="row.rank === 1 ? 'text-amber-400' : 'text-slate-100'">
                                            {{ row.total_skor > 0 ? row.total_skor.toFixed(2) : '–' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <span class="text-xs px-2 py-0.5 rounded-full font-mono" :class="row.judges_submitted >= row.total_judges ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/40' : 'bg-slate-800 text-slate-400'">
                                            {{ row.judges_submitted }}/{{ row.total_judges }} Juri
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
                        <span>Pembaruan otomatis sesuai submission juri.</span>
                        <span v-if="active.status !== 'dikunci'" class="text-amber-400/80 font-medium">* Penilaian masih berlangsung</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
