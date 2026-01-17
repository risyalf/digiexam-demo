<?php

use App\Models\Topic;
use App\Models\User;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by');
            $table->foreignIdFor(User::class, 'deleted_by')->nullable();
            $table->foreignIdFor(Topic::class);
            $table->string('name');
            $table->string('type');
            $table->string('options')->nullable();
            $table->string('file')->nullable();
            $table->string('file_mime')->nullable();
            $table->boolean('show_once')->default(false);
            $table->integer('difficulty_level')->default(1);
            $table->string('right_answer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
