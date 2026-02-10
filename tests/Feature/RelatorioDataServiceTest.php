<?php

namespace Tests\Feature;

use App\Services\RelatorioDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RelatorioDataServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_monta_agregacoes_do_relatorio(): void
    {
        $questionnaireId = DB::table('questionnaires')->insertGetId([
            'title' => 'Q',
            'description' => 'D',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $surveyId = $questionnaireId;

        $audienceA = DB::table('audiences')->insertGetId([
            'name' => 'Docentes',
            'slug' => 'docentes',
            'intro_text' => 'x',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $audienceB = DB::table('audiences')->insertGetId([
            'name' => 'Discentes',
            'slug' => 'discentes',
            'intro_text' => 'x',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dimension1 = DB::table('dimensions')->insertGetId([
            'survey_id' => $surveyId,
            'audience_id' => $audienceA,
            'title' => 'Planejamento',
            'description' => null,
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dimension2 = DB::table('dimensions')->insertGetId([
            'survey_id' => $surveyId,
            'audience_id' => $audienceB,
            'title' => 'Infraestrutura',
            'description' => null,
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question1 = DB::table('questions')->insertGetId([
            'survey_id' => $surveyId,
            'dimension_id' => $dimension1,
            'text' => 'Q1',
            'type' => 'radio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question2 = DB::table('questions')->insertGetId([
            'survey_id' => $surveyId,
            'dimension_id' => $dimension2,
            'text' => 'Q2',
            'type' => 'radio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('options')->insert([
            ['question_id' => $question1, 'text' => 'A'],
            ['question_id' => $question1, 'text' => 'B'],
            ['question_id' => $question1, 'text' => 'C'],
            ['question_id' => $question2, 'text' => 'C'],
            ['question_id' => $question2, 'text' => 'D'],
        ]);

        $response1 = DB::table('responses')->insertGetId([
            'survey_id' => $surveyId,
            'audience_id' => $audienceA,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response2 = DB::table('responses')->insertGetId([
            'survey_id' => $surveyId,
            'audience_id' => $audienceA,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response3 = DB::table('responses')->insertGetId([
            'survey_id' => $surveyId,
            'audience_id' => $audienceB,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('answers')->insert([
            ['response_id' => $response1, 'question_id' => $question1, 'value' => 'A'],
            ['response_id' => $response2, 'question_id' => $question1, 'value' => 'B'],
            ['response_id' => $response3, 'question_id' => $question2, 'value' => 'C'],
        ]);

        $service = app(RelatorioDataService::class);

        $this->assertSame([
            ['publico' => 'Discentes', 'total_perguntados' => 1],
            ['publico' => 'Docentes', 'total_perguntados' => 2],
        ], $service->totaisPorPublico());

        $this->assertSame([
            ['publico' => 'Discentes', 'dimensao' => 'Infraestrutura', 'total_respostas' => 1],
            ['publico' => 'Docentes', 'dimensao' => 'Planejamento', 'total_respostas' => 2],
        ], $service->respostasPorDimensao());

        $this->assertSame([
            ['question_id' => $question1, 'pergunta' => 'Q1', 'resposta' => 'A', 'total_respostas' => 1, 'percentual' => '50,00%'],
            ['question_id' => $question1, 'pergunta' => 'Q1', 'resposta' => 'B', 'total_respostas' => 1, 'percentual' => '50,00%'],
            ['question_id' => $question1, 'pergunta' => 'Q1', 'resposta' => 'C', 'total_respostas' => 0, 'percentual' => '0,00%'],
            ['question_id' => $question2, 'pergunta' => 'Q2', 'resposta' => 'C', 'total_respostas' => 1, 'percentual' => '100,00%'],
            ['question_id' => $question2, 'pergunta' => 'Q2', 'resposta' => 'D', 'total_respostas' => 0, 'percentual' => '0,00%'],
        ], $service->respostasPorPerguntaComPercentuais());
    }
}
