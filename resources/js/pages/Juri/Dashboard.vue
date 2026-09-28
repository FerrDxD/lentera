<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

defineProps<{
    assignedLomba: {
        id: number;
        nama: string;
        slug: string;
        deskripsi: string | null;
        status: string;
        locked_at: string | null;
        peserta_count: number;
        kriteria_count: number;
        rated_count: number;
        is_all_rated: boolean;
    }[];
}>();
</script>

<template>
    <Head title="Dashboard Juri" />
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold">Lomba yang Saya Nilai</h1>

        <div v-if="!assignedLomba.length" class="text-center py-16 text-gray-500">
            <p class="text-4xl mb-3">🎯</p>
            <p>Anda belum ditugaskan pada lomba apapun.</p>
            <p class="text-sm mt-1">Hubungi panitia untuk mendapatkan penugasan.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div
                v-for="l in assignedLomba"
                :key="l.id"
                class="bg-gray-900 rounded-xl p-5 border border-gray-800 space-y-4"
            >
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-semibold text-white leading-tight">{{ l.nama }}</h2>
                    <span
                        class="text-xs px-2 py-0.5 rounded-full shrink-0"
                        :class="l.status === 'dikunci' ? 'bg-red-900/40 text-red-300' : 'bg-green-900/40 text-green-300'"
                    >
                        {{ l.status === 'dikunci' ? '🔒 Dikunci' : '🟢 Aktif' }}
                    </span>
                </div>

                <p v-if="l.deskripsi" class="text-sm text-gray-400 line-clamp-2">{{ l.deskripsi }}</p>

                <!-- Progress -->
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress saya</span>
                        <span :class="l.is_all_rated ? 'text-green-400' : 'text-amber-400'">
                            {{ l.rated_count }}/{{ l.peserta_count }} peserta
                            {{ l.is_all_rated ? '✅' : '' }}
                        </span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div
                            class="h-full transition-all"
                            :class="l.is_all_rated ? 'bg-green-500' : 'bg-amber-500'"
                            :style="{ width: l.peserta_count > 0 ? (l.rated_count / l.peserta_count * 100) + '%' : '0%' }"
                        />
                    </div>
                </div>

                <div class="flex gap-3 items-center">
                    <Link
                        v-if="l.status !== 'dikunci'"
                        :href="`/juri/nilai/${l.id}`"
                        class="text-sm px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg hover:bg-amber-400 transition"
                    >
                        {{ l.rated_count > 0 ? 'Lanjut Nilai' : 'Mulai Nilai' }}
                    </Link>
                    <Link
                        v-else
                        :href="`/juri/nilai/${l.id}`"
                        class="text-sm px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition"
                    >
                        Lihat Nilai
                    </Link>
                    <span class="text-xs text-gray-500">{{ l.kriteria_count }} kriteria</span>
                </div>
            </div>
        </div>
    </div>
</template>
