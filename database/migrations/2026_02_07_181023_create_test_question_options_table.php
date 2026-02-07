<?php

use App\Models\TestQuestion;
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
        Schema::create('test_question_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(User::class, 'created_by')->index();
            $table->foreignIdFor(User::class, 'updated_by')->index();
            $table->foreignIdFor(User::class, 'deleted_by')->index()->nullable();
            $table->foreignIdFor(TestQuestion::class)->index()->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->boolean('value')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_question_options');
    }
};
