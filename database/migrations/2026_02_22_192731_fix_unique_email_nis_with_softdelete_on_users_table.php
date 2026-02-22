<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            // Drop unique lama
            $table->dropUnique("users_email_unique");
            $table->dropUnique("users_nis_unique");

            // Tambah composite unique baru
            $table->unique(
                ["email", "deleted_at"],
                "users_email_deleted_at_unique",
            );
            $table->unique(
                ["nis", "deleted_at"],
                "users_nis_deleted_at_unique",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            // Drop composite unique
            $table->dropUnique("users_email_deleted_at_unique");
            $table->dropUnique("users_nis_deleted_at_unique");

            // Balik ke unique lama
            $table->unique("email");
            $table->unique("nis");
        });
    }
};
