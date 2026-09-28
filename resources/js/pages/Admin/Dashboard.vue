<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stats: {
        total_lomba: number;
        lomba_aktif: number;
        lomba_dikunci: number;
        total_peserta: number;
        total_juri: number;
    };
    lombaList: {
        id: number;
        nama: string;
        slug: string;
        status: string;
        locked_at: string | null;
        kriteria_count: number;
        peserta_count: number;
        juri_count: number;
        completed_penilaian: number;
        total_possible_penilaian: number;
        progress_percent: number;
    }[];
}>();

function statusClass(s: string) {
    if (s === 'dikunci') return 'bg-red-900/40 text-red-300 border border-red-700';
    if (s === 'aktif') return 'bg-green-900/40 text-green-300 border border-green-700';
    return 'bg-gray-800 text-gray-400 border border-gray-700';
}
function statusLabel(s: string) {
    return s === 'dikunci' ? 'Dikunci' : s === 'aktif' ? 'Aktif' : 'Draft';
}
</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold">Dashboard Admin</h1>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <div class="text-3xl font-bold text-amber-400">{{ stats.total_lomba }}</div>
                <div class="text-sm text-gray-400 mt-1">Total Lomba</div>
            </div>
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <div class="text-3xl font-bold text-green-400">{{ stats.lomba_aktif }}</div>
                <div class="text-sm text-gray-400 mt-1">Aktif</div>
            </div>
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <div class="text-3xl font-bold text-red-400">{{ stats.lomba_dikunci }}</div>
                <div class="text-sm text-gray-400 mt-1">Dikunci</div>
            </div>
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <div class="text-3xl font-bold text-blue-400">{{ stats.total_peserta }}</div>
                <div class="text-sm text-gray-400 mt-1">Total Peserta</div>
            </div>
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <div class="text-3xl font-bold text-purple-400">{{ stats.total_juri }}</div>
                <div class="text-sm text-gray-400 mt-1">Total Juri</div>
            </div>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Daftar Lomba</h2>
            <Link
                href="/admin/lomba"
                class="text-sm px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg hover:bg-amber-400 transition"
            >
                + Kelola Lomba
            </Link>
        </div>

        <!-- Lomba cards -->
        <div v-if="!lombaList.length" class="text-center py-16 text-gray-500">
            <p class="text-3xl mb-2">📋</p>
            <p>Belum ada lomba. <Link href="/admin/lomba" class="underline text-amber-400">Buat lomba pertama</Link>.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div
                v-for="lomba in lombaList"
                :key="lomba.id"
                class="bg-gray-900 rounded-xl p-5 border border-gray-800 space-y-3"
            >
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-semibold text-white leading-tight">{{ lomba.nama }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full shrink-0" :class="statusClass(lomba.status)">
                        {{ statusLabel(lomba.status) }}
                    </span>
                </div>

                <div class="flex gap-4 text-sm text-gray-400">
                    <span>🏆 {{ lomba.peserta_count }} peserta</span>
                    <span>👤 {{ lomba.juri_count }} juri</span>
                    <span>📊 {{ lomba.kriteria_count }} kriteria</span>
                </div>

                <!-- Progress bar -->
                <div v-if="lomba.total_possible_penilaian > 0">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress penilaian</span>
                        <span>{{ lomba.completed_penilaian }}/{{ lomba.total_possible_penilaian }} ({{ lomba.progress_percent }}%)</span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-amber-500 transition-all"
                            :style="{ width: lomba.progress_percent + '%' }"
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 flex-wrap pt-1">
                    <Link :href="`/admin/lomba/${lomba.id}/rekap`" class="text-xs px-3 py-1.5 bg-blue-900/40 text-blue-300 border border-blue-700 rounded-lg hover:bg-blue-900/70 transition">
                        Rekap
                    </Link>
                    <Link :href="`/admin/lomba/${lomba.id}/peserta`" class="text-xs px-3 py-1.5 bg-gray-800 text-gray-300 border border-gray-700 rounded-lg hover:bg-gray-700 transition">
                        Peserta
                    </Link>
                    <Link :href="`/admin/lomba/${lomba.id}/kriteria`" class="text-xs px-3 py-1.5 bg-gray-800 text-gray-300 border border-gray-700 rounded-lg hover:bg-gray-700 transition">
                        Kriteria
                    </Link>
                    <Link :href="`/admin/lomba/${lomba.id}/juri`" class="text-xs px-3 py-1.5 bg-gray-800 text-gray-300 border border-gray-700 rounded-lg hover:bg-gray-700 transition">
                        Juri
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
