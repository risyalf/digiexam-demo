<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get("/", function () {
    return Redirect("admin/login");
});

Route::get("/ping", fn() => "ok");

Route::get("/login", function () {
    return redirect("/admin/login");
})->name("login");

Route::get("/download", function () {
    return view("download");
});

// Route::get("/download/apk/new/{filename}", function ($filename) {
//     if (!preg_match('/^[a-zA-Z0-9._-]+\.apk$/', $filename)) {
//         abort(400);
//     }

//     $apkPath = public_path("apk/new/$filename");
//     if (!File::exists($apkPath)) {
//         abort(404);
//     }

//     return response()->download($apkPath, null, [
//         "Content-Type" => "application/vnd.android.package-archive",
//         "Content-Disposition" => "attachment; filename=\"$filename\"",
//     ]);
// })->name("download.apk.new.filename");

// Route::get("/download/apk/old/{filename}", function ($filename) {
//     if (!preg_match('/^[a-zA-Z0-9._-]+\.apk$/', $filename)) {
//         abort(400);
//     }

//     $apkPath = public_path("apk/old/$filename");
//     if (!File::exists($apkPath)) {
//         abort(404);
//     }

//     return response()->download($apkPath, null, [
//         "Content-Type" => "application/vnd.android.package-archive",
//         "Content-Disposition" => "attachment; filename=\"$filename\"",
//     ]);
// })->name("download.apk.old.filename");
