<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: { id: number; nama: string; status: string };
    peserta: { id: number; nomor_urut: string; nama: string; asal_kelas: string; penilaian_count: number }[];
}>();

const showForm = ref(false);
const addForm = useForm({ nomor_urut: '', nama: '', asal_kelas: '' });
const editId = ref<number | null>(null);
const editForm = useForm({ nomor_urut: '', nama: '', asal_kelas: '' });

function submitAdd() {
    addForm.post(`/admin/lomba/${props.lomba.id}/peserta`, { onSuccess: () => { addForm.reset(); showForm.value = false; } });
}
function startEdit(p: typeof props.peserta[0]) {
    editId.value = p.id;
    editForm.nomor_urut = p.nomor_urut;
    editForm.nama = p.nama;
    editForm.asal_kelas = p.asal_kelas;
}
function submitEdit(id: number) {
    editForm.put(`/admin/lomba/${props.lomba.id}/peserta/${id}`, { onSuccess: () => { editId.value = null; } });
}
function deletePeserta(id: number, nama: string) {
    if (!confirm(`Hapus peserta "${nama}"?`)) return;
    router.delete(`/admin/lomba/${props.lomba.id}/peserta/${id}`);
}
</script>

<template>
    <Head :title="`Peserta – ${lomba.nama}`" />
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <Link href="/admin/lomba" class="text-gray-400 hover:text-white text-sm">← Lomba</Link>
                <span class="text-gray-600">/</span>
                <h1 class="text-xl font-bold">Peserta: {{ lomba.nama }}</h1>
            </div>
            <button @click="showForm = !showForm" class="text-sm px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg hover:bg-amber-400 transition">
                {{ showForm ? 'Batal' : '+ Peserta' }}
            </button>
        </div>

        <!-- Add form -->
        <form v-if="showForm" @submit.prevent="submitAdd" class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <h3 class="font-semibold mb-3">Tambah Peserta</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-400 block mb-1">No. Urut *</label>
                    <input v-model="addForm.nomor_urut" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" placeholder="01" />
                </div>
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Nama Peserta *</label>
                    <input v-model="addForm.nama" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" placeholder="Nama lengkap" />
                </div>
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Asal Kelas *</label>
                    <input v-model="addForm.asal_kelas" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" placeholder="XI-MIPA 1" />
                </div>
            </div>
            <button type="submit" :disabled="addForm.processing" class="mt-3 px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                Simpan
            </button>
        </form>

        <!-- Table -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <div v-if="!peserta.length" class="p-8 text-center text-gray-500">Belum ada peserta.</div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-800 text-left">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Nilai Masuk</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in peserta" :key="p.id" class="border-b border-gray-800/50">
                        <template v-if="editId === p.id">
                            <td class="px-4 py-2"><input v-model="editForm.nomor_urut" class="w-16 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" /></td>
                            <td class="px-4 py-2"><input v-model="editForm.nama" class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" /></td>
                            <td class="px-4 py-2"><input v-model="editForm.asal_kelas" class="w-32 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-sm" /></td>
                            <td class="px-4 py-2 text-gray-400">{{ p.penilaian_count }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <button @click="submitEdit(p.id)" class="text-xs px-2 py-1 bg-green-900/40 text-green-300 rounded">Simpan</button>
                                    <button @click="editId = null" class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded">Batal</button>
                                </div>
                            </td>
                        </template>
                        <template v-else>
                            <td class="px-4 py-3 font-mono text-gray-300">{{ p.nomor_urut }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ p.nama }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ p.asal_kelas }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ p.penilaian_count }} entri</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button @click="startEdit(p)" class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600">Edit</button>
                                    <button @click="deletePeserta(p.id, p.nama)" class="text-xs px-2 py-1 bg-red-900/40 text-red-300 rounded hover:bg-red-900/70">Hapus</button>
                                </div>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
