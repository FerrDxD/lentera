<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: { id: number; nama: string; status: string };
    kriteria: { id: number; nama_kriteria: string; bobot: number; skala_max: number }[];
    totalBobot: number;
}>();

const addForm = useForm({ nama_kriteria: '', bobot: 20, skala_max: 100 });
const editId = ref<number | null>(null);
const editForm = useForm({ nama_kriteria: '', bobot: 0, skala_max: 100 });

function startEdit(k: typeof props.kriteria[0]) {
    editId.value = k.id;
    editForm.nama_kriteria = k.nama_kriteria;
    editForm.bobot = k.bobot;
    editForm.skala_max = k.skala_max;
}
function cancelEdit() { editId.value = null; }

function submitAdd() {
    addForm.post(`/admin/lomba/${props.lomba.id}/kriteria`, { onSuccess: () => addForm.reset() });
}
function submitEdit(id: number) {
    editForm.put(`/admin/lomba/${props.lomba.id}/kriteria/${id}`, { onSuccess: () => { editId.value = null; } });
}
function deleteKriteria(id: number, nama: string) {
    if (!confirm(`Hapus kriteria "${nama}"?`)) return;
    router.delete(`/admin/lomba/${props.lomba.id}/kriteria/${id}`);
}
</script>

<script lang="ts">
import { ref } from 'vue';
</script>

<template>
    <Head :title="`Kriteria – ${lomba.nama}`" />
    <div class="p-6 space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="`/admin/lomba`" class="text-gray-400 hover:text-white text-sm">← Lomba</Link>
            <span class="text-gray-600">/</span>
            <h1 class="text-xl font-bold">Kriteria: {{ lomba.nama }}</h1>
        </div>

        <!-- Total bobot indicator -->
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-400">Total bobot:</span>
            <span :class="totalBobot === 100 ? 'text-green-400' : 'text-amber-400'" class="font-bold">{{ totalBobot }}</span>
            <span class="text-gray-600">(idealnya 100)</span>
        </div>

        <!-- List -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <div v-if="!kriteria.length" class="p-8 text-center text-gray-500">
                Belum ada kriteria. Tambahkan di bawah.
            </div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-800 text-left">
                        <th class="px-4 py-3">Nama Kriteria</th>
                        <th class="px-4 py-3 w-24">Bobot</th>
                        <th class="px-4 py-3 w-24">Skala Max</th>
                        <th class="px-4 py-3 w-32"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="k in kriteria" :key="k.id" class="border-b border-gray-800/50">
                        <template v-if="editId === k.id">
                            <td class="px-4 py-2">
                                <input v-model="editForm.nama_kriteria" class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model="editForm.bobot" type="number" min="0.1" step="0.1" class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model="editForm.skala_max" type="number" min="10" class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" />
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <button @click="submitEdit(k.id)" class="text-xs px-2 py-1 bg-green-900/40 text-green-300 rounded">Simpan</button>
                                <button @click="cancelEdit" class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded">Batal</button>
                            </td>
                        </template>
                        <template v-else>
                            <td class="px-4 py-3 text-white">{{ k.nama_kriteria }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ k.bobot }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ k.skala_max }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button @click="startEdit(k)" class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600">Edit</button>
                                    <button @click="deleteKriteria(k.id, k.nama_kriteria)" class="text-xs px-2 py-1 bg-red-900/40 text-red-300 rounded hover:bg-red-900/70">Hapus</button>
                                </div>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add form -->
        <form @submit.prevent="submitAdd" class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <h3 class="font-semibold mb-3">Tambah Kriteria</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Nama Kriteria *</label>
                    <input v-model="addForm.nama_kriteria" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" placeholder="Penghayatan & Ekspresi" />
                    <p v-if="addForm.errors.nama_kriteria" class="text-red-400 text-xs mt-1">{{ addForm.errors.nama_kriteria }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Bobot *</label>
                    <input v-model="addForm.bobot" type="number" min="0.1" step="0.1" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Skala Max *</label>
                    <input v-model="addForm.skala_max" type="number" min="10" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" />
                </div>
            </div>
            <button type="submit" :disabled="addForm.processing" class="mt-3 px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                + Tambah Kriteria
            </button>
        </form>
    </div>
</template>
