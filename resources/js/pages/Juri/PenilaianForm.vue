<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: {
        id: number;
        nama: string;
        deskripsi: string | null;
        status: string;
        is_locked: boolean;
    };
    kriteria: { id: number; nama_kriteria: string; bobot: number; skala_max: number }[];
    pesertaList: {
        id: number;
        nomor_urut: string;
        nama: string;
        asal_kelas: string;
        scores: Record<string, number | null>;
        total_score: number;
        is_rated: boolean;
    }[];
    activePesertaId: number | null;
}>();

const activePesertaId = ref(props.activePesertaId ?? props.pesertaList[0]?.id ?? null);
const activePeserta = computed(() => props.pesertaList.find(p => p.id === activePesertaId.value));

// Initialize form with current scores or 0
const buildScores = (peserta: typeof props.pesertaList[0] | undefined) => {
    const s: Record<string, number | string> = {};
    if (!peserta) return s;
    props.kriteria.forEach(k => {
        s[k.id] = peserta.scores[k.id] ?? 0;
    });
    return s;
};

const form = useForm({ scores: buildScores(activePeserta.value) });

watch(activePeserta, (p) => {
    form.scores = buildScores(p) as any;
}, { immediate: false });

// Live total from current form inputs
const liveTotal = computed(() => {
    const totalBobot = props.kriteria.reduce((s, k) => s + k.bobot, 0) || 1;
    return props.kriteria.reduce((sum, k) => {
        const val = Number(form.scores[k.id] ?? 0);
        return sum + (val * k.bobot) / totalBobot;
    }, 0);
});

function selectPeserta(id: number) {
    activePesertaId.value = id;
}

function submit() {
    if (props.lomba.is_locked) return;
    form.post(`/juri/nilai/${props.lomba.id}/${activePesertaId.value}`, {
        onSuccess: () => {
            // Inertia will reload props, no manual refresh needed
        },
    });
}

const ratedCount = computed(() => props.pesertaList.filter(p => p.is_rated).length);
</script>

<template>
    <Head :title="`Nilai – ${lomba.nama}`" />
    <div class="p-4 md:p-6 space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3 flex-wrap">
            <Link href="/juri/dashboard" class="text-gray-400 hover:text-white text-sm">← Dashboard</Link>
            <span class="text-gray-600">/</span>
            <h1 class="text-lg font-bold">{{ lomba.nama }}</h1>
            <span v-if="lomba.is_locked" class="text-xs px-2 py-0.5 rounded-full bg-red-900/40 text-red-300">🔒 Dikunci</span>
        </div>

        <!-- Progress -->
        <div class="flex items-center gap-3 text-sm">
            <div class="flex-1 bg-gray-800 rounded-full h-2 overflow-hidden">
                <div
                    class="h-full bg-green-500 transition-all"
                    :style="{ width: pesertaList.length > 0 ? (ratedCount / pesertaList.length * 100) + '%' : '0%' }"
                />
            </div>
            <span class="text-gray-400 shrink-0">{{ ratedCount }}/{{ pesertaList.length }} sudah dinilai</span>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <!-- Peserta list sidebar -->
            <div class="md:col-span-1 bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-800 text-sm font-semibold text-gray-400">Peserta</div>
                <div class="max-h-[70vh] overflow-y-auto">
                    <button
                        v-for="p in pesertaList"
                        :key="p.id"
                        @click="selectPeserta(p.id)"
                        class="w-full text-left px-4 py-3 border-b border-gray-800/50 last:border-0 transition"
                        :class="activePesertaId === p.id
                            ? 'bg-amber-500/10 border-l-2 border-l-amber-500'
                            : 'hover:bg-gray-800/50'"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-white">{{ p.nomor_urut }}. {{ p.nama }}</div>
                                <div class="text-xs text-gray-400">{{ p.asal_kelas }}</div>
                            </div>
                            <div class="text-right">
                                <div v-if="p.is_rated" class="text-xs text-green-400">✅</div>
                                <div class="text-xs text-gray-500 tabular-nums">{{ p.total_score.toFixed(1) }}</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Scoring form -->
            <div class="md:col-span-2">
                <div v-if="!activePeserta" class="text-center py-16 text-gray-500">Pilih peserta di kiri</div>

                <form v-else @submit.prevent="submit" class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-800">
                        <h2 class="font-semibold text-white">{{ activePeserta.nomor_urut }}. {{ activePeserta.nama }}</h2>
                        <p class="text-sm text-gray-400">{{ activePeserta.asal_kelas }}</p>
                    </div>

                    <div class="p-5 space-y-5">
                        <div v-for="k in kriteria" :key="k.id" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-300">
                                    {{ k.nama_kriteria }}
                                    <span class="text-xs text-gray-500 ml-1">(bobot {{ k.bobot }})</span>
                                </label>
                                <span class="text-sm font-bold text-amber-400 tabular-nums">
                                    {{ form.scores[k.id] ?? 0 }} / {{ k.skala_max }}
                                </span>
                            </div>
                            <!-- Big number input for easy mobile tap -->
                            <input
                                v-model.number="form.scores[k.id]"
                                type="number"
                                :min="0"
                                :max="k.skala_max"
                                step="0.5"
                                :disabled="lomba.is_locked"
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-4 text-2xl text-center font-bold tabular-nums focus:outline-none focus:border-amber-500 disabled:opacity-50"
                                :class="{ 'border-red-600': (form.scores[k.id] ?? 0) > k.skala_max }"
                            />
                            <!-- Range slider for quick adjustment -->
                            <input
                                v-model.number="form.scores[k.id]"
                                type="range"
                                :min="0"
                                :max="k.skala_max"
                                step="0.5"
                                :disabled="lomba.is_locked"
                                class="w-full accent-amber-500 disabled:opacity-40"
                            />
                            <p v-if="form.errors[`scores.${k.id}`]" class="text-red-400 text-xs">{{ form.errors[`scores.${k.id}`] }}</p>
                        </div>
                    </div>

                    <!-- Live total -->
                    <div class="px-5 py-4 bg-gray-800/50 border-t border-gray-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Total Skor (bobot)</div>
                            <div class="text-3xl font-bold text-amber-400 tabular-nums">{{ liveTotal.toFixed(2) }}</div>
                        </div>

                        <button
                            v-if="!lomba.is_locked"
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-4 bg-amber-500 text-gray-950 font-bold rounded-xl text-lg hover:bg-amber-400 transition disabled:opacity-50 active:scale-95"
                        >
                            {{ form.processing ? 'Menyimpan...' : activePeserta.is_rated ? '✅ Perbarui Nilai' : '✅ Simpan Nilai' }}
                        </button>
                        <div v-else class="text-sm text-red-400">🔒 Lomba dikunci</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
