<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Update setting if user is authenticated
        if (auth()->check()) {
            try {
                \App\Models\Setting::updateOrCreate(
                    ['key' => 'language'],
                    ['value' => $locale, 'group' => 'general']
                );
            } catch (\Exception $e) {
                // Ignore if settings table doesn't exist
            }
        }

        // Redirect back with locale preserved
        $redirect = Redirect::back();
        $redirect->with('locale_changed', true);
        return $redirect;
    }
}
