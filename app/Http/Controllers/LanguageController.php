<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    //
    public function swap($locale)
    {
        // check for existing language
        if (in_array($locale, config('app.available_locales'))) {
            session()->put('locale', $locale);
        }
        return redirect()->back();
    }
}
