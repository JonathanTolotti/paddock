<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::all()->pluck('value', 'key');

        if ($settings->has('workshop_logo')) {
            $settings['workshop_logo_url'] = Storage::url($settings['workshop_logo']);
        }

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'workshop_name' => ['nullable', 'string', 'max:255'],
            'workshop_document' => ['nullable', 'string', 'max:255'],
            'workshop_phone' => ['nullable', 'string', 'max:255'],
            'workshop_address' => ['nullable', 'string', 'max:255'],
            'workshop_logo' => ['nullable', 'image', 'max:1024'], // Logo, max 1MB
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'workshop_logo') continue;

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('workshop_logo')) {
            $path = $request->file('workshop_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'workshop_logo'], ['value' => $path]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Configurações salvas com sucesso.');
    }
}
