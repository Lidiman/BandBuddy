<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/up', fn () => response()->json(['status' => 'ok']));

Route::redirect('/', '/login');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Songs
    Route::prefix('songs')->name('songs.')->group(function () {
        Route::get('/', \App\Livewire\Songs\SongIndex::class)->name('index');
        Route::get('/create', \App\Livewire\Songs\SongForm::class)->name('create');
        Route::get('/{song}', \App\Livewire\Songs\SongShow::class)->name('show');
        Route::get('/{song}/edit', \App\Livewire\Songs\SongForm::class)->name('edit');
    });

    // Schedules
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', \App\Livewire\Schedules\ScheduleIndex::class)->name('index');
        Route::get('/create', \App\Livewire\Schedules\ScheduleForm::class)->name('create');
        Route::get('/history', \App\Livewire\Schedules\ScheduleHistory::class)->name('history');
        Route::get('/{schedule}', \App\Livewire\Schedules\ScheduleShow::class)->name('show');
        Route::get('/{schedule}/edit', \App\Livewire\Schedules\ScheduleForm::class)->name('edit');
        Route::get('/{gig}/mode', \App\Livewire\GigMode\GigMode::class)->name('gig-mode');
    });

    // Performances
    Route::prefix('performances')->name('performances.')->group(function () {
        Route::get('/', \App\Livewire\Performances\PerformanceIndex::class)->name('index');
    });

    // Analytics
    Route::get('/analytics', \App\Livewire\Analytics\AnalyticsDashboard::class)->name('analytics');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/profile', \App\Livewire\Settings\Profile::class)->name('profile');
    });
});
