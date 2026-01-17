<?php

use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserGroup;
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
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by');
            $table->foreignIdFor(User::class, 'deleted_by')->nullable();
            $table->foreignIdFor(UserGroup::class);
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('time_test')->default(0);
            $table->float('correct_point')->default(0);
            $table->float('wrong_point')->default(0);
            $table->float('empty_point')->default(0);
            $table->boolean('show_result')->default(false);
            $table->boolean('detail_result')->default(false);
            $table->boolean('need_token')->default(false);
            $table->foreignIdFor(Module::class)->nullable();
            $table->foreignIdFor(Topic::class)->nullable();
            $table->string('type')->nullable();
            $table->integer('difficulty_level')->default(1);
            $table->integer('total_question')->default(1);
            $table->integer('total_answer')->default(1);
            $table->boolean('randomize_question')->default(false);
            $table->string('status')->default('NOT STARTED');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
