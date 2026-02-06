<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;

class CpaSurveySeeder extends Seeder
{
    public function run(): void
    {
        $survey = Survey::create([
            'title' => 'Avaliação Institucional UEAP – CPA',
            'description' => 'Autoavaliação institucional conforme SINAES',
            'active' => true,
        ]);

        $scaleOptions = [
            'Não sei',
            'Não se aplica',
            'Discordo totalmente',
            'Discordo parcialmente',
            'Indiferente',
            'Concordo parcialmente',
            'Concordo totalmente',
        ];

        $dimensions = [
            [
                'name' => 'Dimensão I – Missão e Plano de Desenvolvimento Institucional (PDI)',
                'questions' => [
                    'Conheço a missão da UEAP.',
                    'A missão institucional é clara.',
                    'Conheço o Plano de Desenvolvimento Institucional (PDI).',
                ],
            ],
            // outras dimensões entram aqui
        ];

        foreach ($dimensions as $dimension) {
            foreach ($dimension['questions'] as $text) {
                $question = Question::create([
                    'survey_id' => $survey->id,
                    'dimension' => $dimension['name'],
                    'text' => $text,
                    'type' => 'scale',
                ]);

                foreach ($scaleOptions as $opt) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => $opt,
                    ]);
                }
            }
        }

        // pergunta aberta final
        Question::create([
            'survey_id' => $survey->id,
            'dimension' => 'Considerações finais',
            'text' => 'Deseja registrar alguma sugestão, crítica ou comentário?',
            'type' => 'text',
        ]);
    }
}
