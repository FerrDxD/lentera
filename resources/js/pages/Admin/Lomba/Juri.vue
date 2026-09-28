<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    lomba: { id: number; nama: string; status: string };
    assignedJuris: { id: number; name: string; email: string }[];
    allJuris: { id: number; name: string; email: string }[];
}>();

const assignForm = useForm({ user_id: '' });
const createForm = useForm({ name: '', email: '', password: '' });
const showCreate = ref(false);

const unassignedJuris = computed(() =>
    props.allJuris.filter(j => !props.assignedJuris.find(a => a.id === j.id))
);

function submitAssign() {
    assignForm.post(`/admin/lomba/${props.lomba.id}/juri/assign`, { onSuccess: () => assignForm.reset() });
}
function unassign(userId: number, name: string) {
    if (!confirm(`Cabut tugas juri "${name}"?`)) return;
    router.delete(`/admin/lomba/${props.lomba.id}/juri/${userId}`);
}
function submitCreate() {
    createForm.post('/admin/juri/create', { onSuccess: () => { createForm.reset(); showCreate.value = false; } });
}
</script>

<script lang="ts">
import { computed } from 'vue';
</script>

<template>
    <Head :title="`Juri – ${lomba.nama}`" />
    <div class="p-6 space-y-6">
        <div class="flex items-center gap-3">
            <Link href="/admin/lomba" class="text-gray-400 hover:text-white text-sm">← Lomba</Link>
            <span class="text-gray-600">/</span>
            <h1 class="text-xl font-bold">Juri: {{ lomba.nama }}</h1>
        </div>

        <!-- Assigned juris -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-800 font-semibold text-sm">Juri Ditugaskan ({{ assignedJuris.length }})</div>
            <div v-if="!assignedJuris.length" class="p-6 text-center text-gray-500 text-sm">Belum ada juri ditugaskan.</div>
            <div v-else>
                <div v-for="j in assignedJuris" :key="j.id" class="flex items-center justify-between px-4 py-3 border-b border-gray-800/50 last:border-0">
                    <div>
                        <div class="font-medium text-white text-sm">{{ j.name }}</div>
                        <div class="text-xs text-gray-400">{{ j.email }}</div>
                    </div>
                    <button @click="unassign(j.id, j.name)" class="text-xs px-3 py-1 bg-red-900/40 text-red-300 rounded hover:bg-red-900/70">Cabut</button>
                </div>
            </div>
        </div>

        <!-- Assign existing juri -->
        <form @submit.prevent="submitAssign" class="bg-gray-900 rounded-xl p-5 border border-gray-800">
            <h3 class="font-semibold mb-3 text-sm">Tugaskan Juri yang Ada</h3>
            <div v-if="!unassignedJuris.length" class="text-sm text-gray-500">Semua juri sudah ditugaskan ke lomba ini.</div>
            <div v-else class="flex gap-3">
                <select v-model="assignForm.user_id" class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-amber-500">
                    <option value="">Pilih juri...</option>
                    <option v-for="j in unassignedJuris" :key="j.id" :value="j.id">{{ j.name }} ({{ j.email }})</option>
                </select>
                <button type="submit" :disabled="!assignForm.user_id || assignForm.processing" class="px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                    Tugaskan
                </button>
            </div>
        </form>

        <!-- Create new juri account -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <button @click="showCreate = !showCreate" class="w-full px-4 py-3 flex items-center justify-between text-sm font-semibold text-left hover:bg-gray-800 transition">
                <span>+ Buat Akun Juri Baru</span>
                <span class="text-gray-400">{{ showCreate ? '▲' : '▼' }}</span>
            </button>
            <form v-if="showCreate" @submit.prevent="submitCreate" class="p-5 border-t border-gray-800 space-y-3">
                <div class="grid md:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Nama *</label>
                        <input v-model="createForm.name" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" />
                        <p v-if="createForm.errors.name" class="text-red-400 text-xs mt-1">{{ createForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Email *</label>
                        <input v-model="createForm.email" type="email" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" />
                        <p v-if="createForm.errors.email" class="text-red-400 text-xs mt-1">{{ createForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 block mb-1">Password *</label>
                        <input v-model="createForm.password" type="password" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm" />
                    </div>
                </div>
                <button type="submit" :disabled="createForm.processing" class="px-4 py-2 bg-amber-500 text-gray-950 font-semibold rounded-lg text-sm hover:bg-amber-400 transition disabled:opacity-50">
                    Buat Akun Juri
                </button>
            </form>
        </div>
    </div>
</template>
