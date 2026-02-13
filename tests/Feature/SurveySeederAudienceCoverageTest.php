<?php

namespace Tests\Feature;

use Database\Seeders\SurveySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SurveySeederAudienceCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_cria_novos_publicos_e_dimensoes_esperadas(): void
    {
        $this->seed(SurveySeeder::class);

        $expectedBySlug = [
            'gestao' => 10,
            'alta_gestao' => 10,
            'transposto' => 7,
        ];

        foreach ($expectedBySlug as $slug => $expectedDimensionCount) {
            $audience = DB::table('audiences')->where('slug', $slug)->first();

            $this->assertNotNull($audience, "Público {$slug} não foi criado.");

            $dimensionCount = DB::table('dimensions')
                ->where('audience_id', $audience->id)
                ->count();

            $this->assertSame($expectedDimensionCount, $dimensionCount, "Quantidade de dimensões inesperada para {$slug}.");
        }
    }
}
