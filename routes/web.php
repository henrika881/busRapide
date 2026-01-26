<?php


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.accueil');
});

Route::get('/reservation', function () {
    return view('pages.reservation');
});
Route::get('/paiement', function () {
    return view('pages.paiement');
});
Route::get('/tickets', function () {
    return view('pages.tickets');
});
Route::get('/profil', function () {
    return view('pages.profil');
});

Route::get('/admin/login', function () {
    return view('pages.admin.login');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');