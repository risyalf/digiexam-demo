<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return Redirect('admin/login');
});

Route::get('/download', function () {
    return view('download');
});

Route::get('/download/apk', function () {
    $apkPath = public_path('apk/assessment.apk');

    if (! File::exists($apkPath)) {
        abort(404, 'File APK tidak ditemukan');
    }

    return response()->download($apkPath, 'assessment.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('download.apk');
