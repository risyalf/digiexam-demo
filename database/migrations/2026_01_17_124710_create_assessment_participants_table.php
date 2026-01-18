<?php

use App\Models\Assessment;
use App\Models\AssessmentToken;
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
        Schema::create('assessment_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Assessment::class);
            $table->foreignIdFor(AssessmentToken::class);
            $table->timestamp('start_time');
            $table->string('status');
            $table->float('point')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_participants');
    }
};
