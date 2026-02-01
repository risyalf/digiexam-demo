<?php

use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
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
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(ParticipantGroup::class);
            $table->foreignIdFor(Assessment::class)->nullable();
            $table->foreignIdFor(AssessmentToken::class)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status');
            $table->string('last_status');
            $table->float('point')->default(0);

            $table->unique(['user_id', 'assessment_id']);
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
