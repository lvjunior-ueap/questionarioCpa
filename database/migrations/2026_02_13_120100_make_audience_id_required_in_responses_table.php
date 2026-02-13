<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fallbackAudienceId = DB::table('audiences')->orderBy('id')->value('id');

        if ($fallbackAudienceId) {
            DB::table('responses')->whereNull('audience_id')->update(['audience_id' => $fallbackAudienceId]);
        }

        Schema::table('responses', function (Blueprint $table) {
            $table->foreignId('audience_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->foreignId('audience_id')->nullable()->change();
        });
    }
};
