<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetitionJuryController extends Controller
{
    public function index(Request $request): View
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        // Fetch categories grouped by level for checkboxes
        $categories = CompetitionCategory::when($event, fn ($q) => $q->where('competition_event_id', $event->id))
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('order_position')
            ->get();

        // Fetch all juries
        $juries = User::where('role', 'jury')
            ->with(['assignedCategories' => function ($q) use ($event) {
                if ($event) {
                    $q->where('competition_event_id', $event->id);
                }
            }])
            ->orderByDesc('id')
            ->get();

        return view('admin.competition.juries.index', compact('event', 'allEvents', 'categories', 'juries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:4'],
            'phone' => ['nullable', 'string', 'max:20'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:competition_categories,id'],
        ], [
            'name.required' => 'Nama lengkap juri wajib diisi.',
            'username.required' => 'Username / kode login juri wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'PIN / Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 4 karakter.',
        ]);

        $jury = User::create([
            'name' => $request->input('name'),
            'username' => strtolower(trim((string) $request->input('username'))),
            'email' => strtolower(trim((string) $request->input('username'))).'@juri.pmr',
            'password' => Hash::make((string) $request->input('password')),
            'role' => 'jury',
            'phone' => $request->input('phone'),
        ]);

        if ($request->has('category_ids')) {
            $jury->assignedCategories()->sync($request->input('category_ids'));
        }

        return redirect()->route('admin.competition-juries.index')
            ->with('success', "Akun juri '{$jury->name}' berhasil dibuat dan dipetakan ke cabang lomba.");
    }

    public function update(Request $request, User $jury): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($jury->id)],
            'password' => ['nullable', 'string', 'min:4'],
            'phone' => ['nullable', 'string', 'max:20'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:competition_categories,id'],
        ]);

        $data = [
            'name' => $request->input('name'),
            'username' => strtolower(trim((string) $request->input('username'))),
            'phone' => $request->input('phone'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make((string) $request->input('password'));
        }

        $jury->update($data);

        $jury->assignedCategories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.competition-juries.index')
            ->with('success', "Data akun dan penugasan juri '{$jury->name}' berhasil diperbarui.");
    }

    public function destroy(User $jury): RedirectResponse
    {
        $name = $jury->name;
        $jury->assignedCategories()->detach();
        $jury->delete();

        return redirect()->route('admin.competition-juries.index')
            ->with('success', "Akun juri '{$name}' berhasil dihapus.");
    }
}
