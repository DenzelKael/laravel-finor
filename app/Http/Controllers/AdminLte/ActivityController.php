<?php

namespace App\Http\Controllers\AdminLte;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $activities = Activity::with('user')
            ->when($filters['user_id'] ?? null, fn($q, $id) => $q->where('user_id', $id))
            ->when($filters['from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filters['to'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('adminlte.activity.index', compact('activities', 'users'));
    }
}