<?php

use App\Models\Assessment;
use App\Models\AssessmentToken;
use App\Models\Participant;
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
        Schema::create('participant_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignIdFor(Participant::class)->index()->onDelete('cascade');
            $table->foreignIdFor(Assessment::class)->onDelete('cascade');
            $table->foreignIdFor(AssessmentToken::class)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default('IDLE');
            $table->string('last_status')->default('IDLE');
            $table->float('point')->default(0);
            $table->string('unlock_token')->nullable();

            $table->unique(['participant_id', 'assessment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_assessments');
    }
};
