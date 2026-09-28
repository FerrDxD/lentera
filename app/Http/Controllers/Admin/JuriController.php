<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JuriLomba;
use App\Models\Lomba;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class JuriController extends Controller
{
    public function index(Lomba $lomba): Response
    {
        $assignedJuriIds = $lomba->juri()->pluck('users.id')->toArray();
        $allJuris = User::where('role', 'juri')->get();
        $assignedJuris = $lomba->juri()->get();

        return Inertia::render('Admin/Lomba/Juri', [
            'lomba' => $lomba,
            'assignedJuris' => $assignedJuris,
            'allJuris' => $allJuris,
        ]);
    }

    public function assign(Request $request, Lomba $lomba): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        if ($user->role !== 'juri') {
            return back()->with('error', 'User tersebut bukan merupakan juri.');
        }

        JuriLomba::firstOrCreate([
            'lomba_id' => $lomba->id,
            'user_id' => $user->id,
        ]);

        return back()->with('success', "Juri {$user->name} berhasil ditugaskan.");
    }

    public function unassign(Lomba $lomba, User $user): RedirectResponse
    {
        JuriLomba::where('lomba_id', $lomba->id)
            ->where('user_id', $user->id)
            ->delete();

        return back()->with('success', "Tugas juri {$user->name} berhasil dicabut.");
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'juri',
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'Akun juri baru berhasil dibuat.');
    }
}
