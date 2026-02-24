<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return Redirect('admin/login');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/download', function () {
    return view('download');
});

Route::get('/download/apk/{filename}', function ($filename) {
    if (! preg_match('/^[a-zA-Z0-9._-]+\.apk$/', $filename)) {
        abort(400, 'Invalid filename');
    }

    $apkPath = public_path("apk/$filename");

    if (! File::exists($apkPath)) {
        abort(404, 'File not found');
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $filesize = filesize($apkPath);

    header('Content-Type: application/vnd.android.package-archive');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Content-Length: ' . $filesize);
    header('Accept-Ranges: bytes');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: no-cache');

    $fp = fopen($apkPath, 'rb');

    while (!feof($fp)) {
        echo fread($fp, 1024 * 128);
        flush();
    }

    fclose($fp);
    exit;
})->name('download.apk.filename');