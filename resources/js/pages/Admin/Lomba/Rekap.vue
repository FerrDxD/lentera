<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: {
        id: number;
        nama: string;
        status: string;
        locked_at: string | null;
    };
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
        kriteria_breakdown: Record<string, { avg_skor: number; weighted_skor: number; sample_count: number }>;
    }[];
    totalPeserta: number;
    totalJuri: number;
}>();

function lock() {
    if (!confirm('Kunci lomba ini? Setelah dikunci, juri tidak bisa mengubah skor.')) return;
    router.post(`/admin/lomba/${props.lomba.id}/lock`);
}
function unlock() {
    if (!confirm('Buka kembali penilaian lomba ini?')) return;
    router.post(`/admin/lomba/${props.lomba.id}/unlock`);
}
function printPage() {
    window.print();
}
</script>

<template>
    <Head :title="`Rekap – ${lomba.nama}`" />
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <Link href="/admin/dashboard" class="text-gray-400 hover:text-white text-sm">← Dashboard</Link>
                <span class="text-gray-600">/</span>
                <h1 class="text-xl font-bold">Rekap: {{ lomba.nama }}</h1>
                <span
                    class="text-xs px-2 py-0.5 rounded-full"
                    :class="lomba.status === 'dikunci' ? 'bg-red-900/40 text-red-300' : 'bg-green-900/40 text-green-300'"
                >
                    {{ lomba.status === 'dikunci' ? '🔒 Dikunci' : '🟢 Aktif' }}
                </span>
            </div>
            <div class="flex gap-2">
                <button @click="printPage" class="text-sm px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition print:hidden">
                    🖨 Print
                </button>
                <button v-if="lomba.status !== 'dikunci'" @click="lock" class="text-sm px-4 py-2 bg-red-900/60 text-red-200 rounded-lg hover:bg-red-800 transition print:hidden">
                    🔒 Kunci Hasil
                </button>
                <button v-else @click="unlock" class="text-sm px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition print:hidden">
                    🔓 Buka Kembali
                </button>
            </div>
        </div>

        <!-- Stats bar -->
        <div class="flex gap-6 text-sm text-gray-400">
            <span>👥 {{ totalPeserta }} peserta</span>
            <span>🎯 {{ totalJuri }} juri</span>
            <span>📊 {{ kriteria.length }} kriteria</span>
        </div>

        <!-- Empty state -->
        <div v-if="!leaderboard.length" class="text-center py-16 text-gray-500">
            <p class="text-3xl mb-2">📋</p>
            <p>Belum ada peserta atau penilaian.</p>
        </div>

        <!-- Leaderboard table -->
        <div v-else class="bg-gray-900 rounded-xl border border-gray-800 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-800 text-left">
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">Peserta</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th
                            v-for="k in kriteria"
                            :key="k.id"
                            class="px-4 py-3 text-right text-xs"
                        >
                            {{ k.nama_kriteria }}<br/>
                            <span class="text-gray-600">({{ k.bobot }}%)</span>
                        </th>
                        <th class="px-4 py-3 text-right font-bold">Total</th>
                        <th class="px-4 py-3 text-right text-xs">Juri</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in leaderboard"
                        :key="row.peserta_id"
                        class="border-b border-gray-800/50"
                        :class="{
                            'bg-amber-950/30': row.rank === 1,
                            'bg-gray-800/20': row.rank === 2,
                            'bg-amber-900/10': row.rank === 3,
                        }"
                    >
                        <td class="px-4 py-3 font-bold text-center">
                            {{ row.rank === 1 ? '🥇' : row.rank === 2 ? '🥈' : row.rank === 3 ? '🥉' : row.rank }}
                        </td>
                        <td class="px-4 py-3 font-medium text-white">{{ row.nomor_urut }}. {{ row.nama }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ row.asal_kelas }}</td>
                        <td
                            v-for="k in kriteria"
                            :key="k.id"
                            class="px-4 py-3 text-right tabular-nums"
                        >
                            <template v-if="row.kriteria_breakdown[k.id]?.sample_count > 0">
                                <div>{{ row.kriteria_breakdown[k.id].avg_skor.toFixed(1) }}</div>
                                <div class="text-xs text-gray-500">({{ row.kriteria_breakdown[k.id].weighted_skor.toFixed(2) }})</div>
                            </template>
                            <span v-else class="text-gray-600">–</span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold tabular-nums text-amber-300">
                            {{ row.total_skor > 0 ? row.total_skor.toFixed(2) : '–' }}
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-gray-500">
                            {{ row.judges_submitted }}/{{ row.total_judges }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-if="lomba.status !== 'dikunci'" class="text-xs text-gray-600 text-right">
            * Penilaian masih berlangsung. Total = rata-rata antar juri berbobot per kriteria.
        </p>
    </div>
</template>
