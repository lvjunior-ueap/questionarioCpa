<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->foreignId('audience_id')->constrained()->cascadeOnDelete();
            $table->json('answers')->nullable();
            $table->json('seen_dimensions')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['audience_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_sessions');
    }
};
