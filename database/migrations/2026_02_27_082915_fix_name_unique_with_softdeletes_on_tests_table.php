<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("tests", function (Blueprint $table) {
            // Drop unique lama
            $table->dropUnique("tests_name_unique");

            // Tambah composite unique baru
            $table->unique(
                ["name", "deleted_at"],
                "tests_name_deleted_at_unique",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tests", function (Blueprint $table) {
            // Drop composite unique
            $table->dropUnique("tests_name_deleted_at_unique");

            // Balik ke unique lama
            $table->unique("name");
        });
    }
};
