<?php

use App\Models\Assessment;
use App\Models\ParticipantGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("assessment_participant_groups", function (
            Blueprint $table,
        ) {
            $table->foreignIdFor(Assessment::class);
            $table->foreignIdFor(ParticipantGroup::class);

            $table->unique(
                ["assessment_id", "participant_group_id"],
                "apg_assessment_pg_unique",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("assessment_participant_groups");
    }
};
