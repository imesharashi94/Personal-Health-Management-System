<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $consents = Consent::where('user_id', $user->id)
            ->orderBy('granted_at', 'desc')
            ->get();

        return Inertia::render('Settings/Index', [
            'user' => $user,
            'consents' => $consents,
        ]);
    }

    public function toggleConsent(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'scopes' => 'required|array',
            'action' => 'required|in:grant,revoke',
        ]);

        $user = $request->user();

        if ($validated['action'] === 'grant') {
            Consent::create([
                'user_id' => $user->id,
                'provider' => $validated['provider'],
                'scopes' => $validated['scopes'],
                'granted_at' => now(),
            ]);

            return back()->with('success', 'Consent granted successfully.');
        } else {
            Consent::where('user_id', $user->id)
                ->where('provider', $validated['provider'])
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            return back()->with('success', 'Consent revoked successfully.');
        }
    }
}

