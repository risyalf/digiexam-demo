<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return Redirect('admin/login');
});

Route::get('/download', function () {
    return view('download');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/download/apk/{filename}', function ($filename) {
    if (! preg_match('/^[a-zA-Z0-9._-]+\.apk$/', $filename)) {
        abort(400, 'Nama file tidak valid');
    }

    $apkPath = public_path("apk/$filename");

    if (! File::exists($apkPath)) {
        abort(404, 'File APK tidak ditemukan');
    }

    return response()->streamDownload(function () use ($apkPath) {
        $stream = fopen($apkPath, 'rb');
        while (!feof($stream)) {
            echo fread($stream, 1024 * 32); // stream 32KB
            flush(); // kirim chunk ke client
        }
        fclose($stream);
    }, $filename, [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Length' => filesize($apkPath), // optional, lebih kompatibel
    ]);
})->name('download.apk.dynamic');
