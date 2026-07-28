<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::getAllCached();
        $identifiers = \DateTimeZone::listIdentifiers();
        $timezones = [];
        
        foreach ($identifiers as $identifier) {
            $parts = explode('/', $identifier, 2);
            if (count($parts) > 1) {
                $timezones[$parts[0]][] = $identifier;
            } else {
                $timezones['Other'][] = $identifier;
            }
        }
        
        return view('admin.settings.index', compact('settings', 'timezones'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'email_from_name' => 'nullable|string|max:255',
            'email_from_address' => 'nullable|email|max:255',
            'default_currency' => 'required|in:USD,IQD',
            'default_locale' => 'required|in:en,ar,ku',
            'timezone' => 'nullable|string|max:100',
        ]);

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:png,jpg,jpeg,svg|max:2048',
            ]);
            
            // Delete old logo if exists
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            // Store new logo
            $path = $request->file('company_logo')->store('branding', 'public');
            Setting::set('company_logo', $path, 'string', 'branding');
        }

        // Save each setting
        Setting::set('company_name', $validated['company_name'], 'string', 'branding');
        Setting::set('primary_color', $validated['primary_color'], 'string', 'branding');
        Setting::set('secondary_color', $validated['secondary_color'], 'string', 'branding');
        Setting::set('email_from_name', $validated['email_from_name'] ?? '', 'string', 'email');
        Setting::set('email_from_address', $validated['email_from_address'] ?? '', 'string', 'email');
        Setting::set('default_currency', $validated['default_currency'], 'string', 'general');
        Setting::set('default_locale', $validated['default_locale'], 'string', 'general');
        Setting::set('timezone', $validated['timezone'] ?? 'Asia/Baghdad', 'string', 'general');

        // If the default language is changed, update the current session to reflect it immediately
        if ($request->has('default_locale') && in_array($request->default_locale, ['en', 'ar', 'ku'])) {
            \Illuminate\Support\Facades\Session::put('locale', $request->default_locale);
            \Illuminate\Support\Facades\App::setLocale($request->default_locale);
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')->with('success', __('Settings saved successfully.'));
    }

    /**
     * Remove the company logo.
     */
    public function removeLogo()
    {
        $logo = Setting::get('company_logo');
        
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }
        
        Setting::set('company_logo', '', 'string', 'branding');
        Setting::clearCache();

        return redirect()->route('admin.settings.index')->with('success', __('Logo removed successfully.'));
    }
}
