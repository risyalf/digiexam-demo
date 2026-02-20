<?php

use App\Models\Test;
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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(User::class, 'created_by')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'updated_by')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'deleted_by')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Test::class)->constrained()->cascadeOnDelete();
            $table->text('name');
            $table->string('type');
            $table->integer('ordering');
            $table->string('file')->nullable();
            $table->boolean('show_once')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
