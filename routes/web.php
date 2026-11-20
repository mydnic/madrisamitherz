<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, site_locales(), true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('language.switch');

Route::redirect('/login', '/admin/login')->name('login');

Route::view('/', 'landing')->name('landing');
Route::post('/kontakt', ContactController::class)->middleware('throttle:5,1')->name('contact.send');
Route::view('/impressum', 'legal', ['field' => 'legal.impressum'])->name('impressum');
Route::view('/datenschutz', 'legal', ['field' => 'legal.datenschutz'])->name('datenschutz');
