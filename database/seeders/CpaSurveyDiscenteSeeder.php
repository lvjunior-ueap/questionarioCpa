<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;

class CpaSurveyDiscenteSeeder extends Seeder
{
    public function run(): void
    {
        $survey = Survey::create([
            'title' => 'Avaliação Institucional UEAP – Discente',
            'description' => 'Autoavaliação institucional – segmento discente (CPA)',
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

            // DIMENSÃO I
            [
                'order' => 1,
                'dimension' => 'Dimensão I – Missão e Plano de Desenvolvimento Institucional (PDI)',
                'questions' => [
                    'Conheço a missão da UEAP.',
                    'A missão institucional é clara.',
                    'As ações da universidade estão alinhadas à sua missão.',
                    'Conheço o Plano de Desenvolvimento Institucional (PDI).',
                ],
            ],

            // DIMENSÃO II
            [
                'order' => 2,
                'dimension' => 'Dimensão II – Políticas para o Ensino',
                'questions' => [
                    'O projeto pedagógico do curso atende às expectativas.',
                    'Os conteúdos das disciplinas são adequados.',
                    'Os métodos de ensino utilizados favorecem a aprendizagem.',
                    'Os critérios de avaliação são claros.',
                ],
            ],

            // DIMENSÃO III
            [
                'order' => 3,
                'dimension' => 'Dimensão III – Responsabilidade Social',
                'questions' => [
                    'A UEAP desenvolve ações voltadas à responsabilidade social.',
                    'As ações extensionistas contribuem para a formação discente.',
                    'A universidade demonstra compromisso com a comunidade.',
                ],
            ],

            // DIMENSÃO IV
            [
                'order' => 4,
                'dimension' => 'Dimensão IV – Comunicação com a Sociedade',
                'questions' => [
                    'As informações institucionais são divulgadas de forma clara.',
                    'Os canais de comunicação da UEAP são eficientes.',
                    'Tenho fácil acesso às informações acadêmicas.',
                ],
            ],

            // DIMENSÃO V
            [
                'order' => 5,
                'dimension' => 'Dimensão V – Políticas de Atendimento aos Discentes',
                'questions' => [
                    'A universidade oferece políticas adequadas de apoio ao estudante.',
                    'Os serviços de assistência estudantil atendem às necessidades.',
                    'Os serviços de apoio pedagógico são eficientes.',
                    'Os canais de atendimento ao discente funcionam adequadamente.',
                ],
            ],

            // DIMENSÃO VI
            [
                'order' => 6,
                'dimension' => 'Dimensão VI – Organização e Gestão da Instituição',
                'questions' => [
                    'A gestão da universidade é transparente.',
                    'As normas acadêmicas são claras.',
                    'Os processos administrativos são eficientes.',
                ],
            ],

            // DIMENSÃO VII
            [
                'order' => 7,
                'dimension' => 'Dimensão VII – Infraestrutura Física',
                'questions' => [
                    'As salas de aula apresentam boas condições.',
                    'Os laboratórios atendem às necessidades do curso.',
                    'A biblioteca atende às demandas acadêmicas.',
                    'Os recursos tecnológicos são adequados.',
                ],
            ],

            // DIMENSÃO IX (visão discente)
            [
                'order' => 9,
                'dimension' => 'Dimensão IX – Políticas de Pessoal (visão discente)',
                'questions' => [
                    'Os docentes demonstram domínio do conteúdo.',
                    'Os docentes são acessíveis para esclarecimento de dúvidas.',
                    'Os técnicos-administrativos prestam bom atendimento.',
                ],
            ],
        ];

        foreach ($dimensions as $block) {
            foreach ($block['questions'] as $text) {
                $question = Question::create([
                    'survey_id'       => $survey->id,
                    'dimension'       => $block['dimension'],
                    'dimension_order' => $block['order'],
                    'target_perfil'   => 'discente',
                    'text'            => $text,
                    'type'            => 'scale',
                ]);
        
                foreach ($scaleOptions as $opt) {
                    Option::create([
                        'question_id' => $question->id,
                        'text'        => $opt,
                    ]);
                }
            }
        }
        

        // Pergunta aberta final
        Question::create([
            'survey_id'       => $survey->id,
            'dimension'       => 'Considerações finais',
            'dimension_order' => 99,
            'target_perfil'   => 'discente',
            'text'            => 'Deseja registrar alguma sugestão, crítica ou comentário?',
            'type'            => 'text',
        ]);
    }
}
