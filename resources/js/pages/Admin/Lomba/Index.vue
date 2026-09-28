<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: {
        id: number;
        nama: string;
        slug: string;
        deskripsi: string | null;
        status: string;
        locked_at: string | null;
        kriteria_count: number;
        peserta_count: number;
        juri_count: number;
    }[];
}>();

const showCreateForm = ref(false);
const editId = ref<number | null>(null);

const createForm = useForm({
    nama: '',
    deskripsi: '',
    status: 'draft' as 'draft' | 'aktif' | 'dikunci',
});

const editForm = useForm({
    nama: '',
    deskripsi: '',
    status: 'draft' as 'draft' | 'aktif' | 'dikunci',
});

function submitCreate() {
    createForm.post('/admin/lomba', {
        onSuccess: () => { createForm.reset(); showCreateForm.value = false; },
    });
}

function startEdit(l: typeof props.lomba[0]) {
    editId.value = l.id;
    editForm.nama = l.nama;
    editForm.deskripsi = l.deskripsi ?? '';
    editForm.status = l.status as any;
}

function submitEdit(id: number) {
    editForm.put(`/admin/lomba/${id}`, {
        onSuccess: () => { editId.value = null; },
    });
}

function setStatus(id: number, status: 'draft' | 'aktif' | 'dikunci') {
    router.put(`/admin/lomba/${id}`, { status });
}

function deleteLomba(id: number, nama: string) {
    if (!confirm(`Hapus lomba "${nama}"? Semua data terkait akan ikut terhapus.`)) return;
    router.delete(`/admin/lomba/${id}`);
}

function statusClass(s: string) {
    if (s === 'dikunci') return 'bg-red-900/40 text-red-300 border border-red-700';
    if (s === 'aktif') return 'bg-green-900/40 text-green-300 border border-green-700';
    return 'bg-gray-800 text-gray-400 border border-gray-700';
}
</script>

<template>
    <Head title="Kelola Lomba" />
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Kelola Lomba</h1>
            <button
                @click="showCreateForm = !showCreateForm"
                class="px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg hover:bg-amber-400 transition text-sm"
            >
                {{ showCreateForm ? 'Batal' : '+ Lomba Baru' }}
            </button>
        </div>

        <!-- Create Form -->
        <form v-if="showCreateForm" @submit.prevent="submitCreate" class="bg-gray-900 rounded-xl p-5 border border-gray-800 space-y-4">
            <h2 class="font-semibold text-white">Tambah Lomba Baru</h2>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Nama Lomba *</label>
                <input v-model="createForm.nama" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500" placeholder="Contoh: Lomba Baca Puisi" />
                <p v-if="createForm.errors.nama" class="text-red-400 text-xs mt-1">{{ createForm.errors.nama }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Deskripsi</label>
                <textarea v-model="createForm.deskripsi" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500" />
            </div>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Status Awal</label>
                <select v-model="createForm.status" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500">
                    <option value="draft">Draft</option>
                    <option value="aktif">Aktif</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" :disabled="createForm.processing" class="px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                    Simpan Lomba
                </button>
                <button type="button" @click="showCreateForm = false" class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg text-sm hover:bg-gray-600 transition">
                    Batal
                </button>
            </div>
        </form>

        <!-- Edit Modal/Form -->
        <form v-if="editId !== null" @submit.prevent="submitEdit(editId)" class="bg-gray-900 rounded-xl p-5 border border-amber-500/50 space-y-4">
            <h2 class="font-semibold text-amber-400">Edit Lomba</h2>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Nama Lomba *</label>
                <input v-model="editForm.nama" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500" />
            </div>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Deskripsi</label>
                <textarea v-model="editForm.deskripsi" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500" />
            </div>
            <div>
                <label class="text-sm text-gray-400 block mb-1">Status</label>
                <select v-model="editForm.status" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500">
                    <option value="draft">Draft</option>
                    <option value="aktif">Aktif</option>
                    <option value="dikunci">Dikunci</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                    Simpan Perubahan
                </button>
                <button type="button" @click="editId = null" class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg text-sm hover:bg-gray-600 transition">
                    Batal
                </button>
            </div>
        </form>

        <!-- List -->
        <div v-if="!lomba.length" class="text-center py-16 text-gray-500">
            <p class="text-3xl mb-2">📋</p>
            <p>Belum ada lomba.</p>
        </div>

        <div class="overflow-x-auto" v-else>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-800">
                        <th class="text-left pb-2 pr-4">Nama</th>
                        <th class="text-left pb-2 pr-4">Status</th>
                        <th class="text-left pb-2 pr-4">Ubah Status Cepat</th>
                        <th class="text-left pb-2 pr-4">Peserta</th>
                        <th class="text-left pb-2 pr-4">Juri</th>
                        <th class="text-left pb-2 pr-4">Kriteria</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="l in lomba" :key="l.id" class="border-b border-gray-800/50 hover:bg-gray-800/30">
                        <td class="py-3 pr-4 font-medium text-white">{{ l.nama }}</td>
                        <td class="py-3 pr-4">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" :class="statusClass(l.status)">
                                {{ l.status === 'dikunci' ? '🔒 Dikunci' : l.status === 'aktif' ? '🟢 Aktif' : '⚪ Draft' }}
                            </span>
                        </td>
                        <td class="py-3 pr-4">
                            <!-- Quick 1-Click Status Toggle -->
                            <button
                                v-if="l.status === 'draft'"
                                @click="setStatus(l.id, 'aktif')"
                                class="text-xs px-2.5 py-1 bg-green-600 text-white rounded hover:bg-green-500 font-medium transition"
                            >
                                🟢 Aktifkan
                            </button>
                            <button
                                v-else-if="l.status === 'aktif'"
                                @click="setStatus(l.id, 'draft')"
                                class="text-xs px-2.5 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition"
                            >
                                ⚪ Jadikan Draft
                            </button>
                            <span v-else class="text-xs text-gray-500">Terkunci</span>
                        </td>
                        <td class="py-3 pr-4 text-gray-400">{{ l.peserta_count }}</td>
                        <td class="py-3 pr-4 text-gray-400">{{ l.juri_count }}</td>
                        <td class="py-3 pr-4 text-gray-400">{{ l.kriteria_count }}</td>
                        <td class="py-3">
                            <div class="flex gap-2 flex-wrap justify-end">
                                <button @click="startEdit(l)" class="text-xs px-2 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded hover:bg-amber-500/30">Edit</button>
                                <Link :href="`/admin/lomba/${l.id}/rekap`" class="text-xs px-2 py-1 bg-blue-900/40 text-blue-300 rounded hover:bg-blue-900/70">Rekap</Link>
                                <Link :href="`/admin/lomba/${l.id}/peserta`" class="text-xs px-2 py-1 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">Peserta</Link>
                                <Link :href="`/admin/lomba/${l.id}/kriteria`" class="text-xs px-2 py-1 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">Kriteria</Link>
                                <Link :href="`/admin/lomba/${l.id}/juri`" class="text-xs px-2 py-1 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">Juri</Link>
                                <button @click="deleteLomba(l.id, l.nama)" class="text-xs px-2 py-1 bg-red-900/40 text-red-300 rounded hover:bg-red-900/70">Hapus</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
