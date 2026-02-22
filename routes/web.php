<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return Redirect('admin/login');
});

Route::get('/download', function () {
    return view('download');
});

Route::get('/download/apk/{filename}', function ($filename) {

    // Validasi: hanya izinkan .apk dan karakter aman
    if (! preg_match('/^[a-zA-Z0-9._-]+\.apk$/', $filename)) {
        abort(400, 'Nama file tidak valid');
    }

    $apkPath = public_path("apk/$filename");

    if (! File::exists($apkPath)) {
        abort(404, 'File APK tidak ditemukan');
    }

    return response()->download($apkPath, $filename, [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('download.apk.dynamic');
