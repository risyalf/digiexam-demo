<?php

use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
use App\Models\Module;
use App\Models\ParticipantGroup;
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
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignIdFor(Module::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(ParticipantGroup::class)->constrained();
            $table->integer('order_number')->unique()->nullable();
            $table->string('test_number')->unique()->nullable();
            $table->string('test_password')->nullable();

            $table->unique(['user_id', 'participant_group_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
