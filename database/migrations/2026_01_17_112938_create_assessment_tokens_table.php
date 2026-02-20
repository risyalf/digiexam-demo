<?php

use App\Models\Assessment;
use App\Models\Topic;
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
        Schema::create('assessment_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('value');
            $table->float('expired_time')->default(0);
            $table->timestamp('expired_until')->nullable();
            $table->foreignIdFor(Assessment::class)->nullable()->constrained();
            $table->boolean('all_module')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_tokens');
    }
};
