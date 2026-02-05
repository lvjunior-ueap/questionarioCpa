<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Option;

class CpaQuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        // Questionário principal
        $questionnaire = Questionnaire::create([
            'title' => 'Avaliação Institucional UEAP',
            'description' => 'Questionário da Comissão Própria de Avaliação',
            'active' => true
        ]);

        $questions = [
            // 1–10
            'Como você avalia a infraestrutura física da universidade?',
            'Como você avalia as salas de aula?',
            'Como você avalia os laboratórios?',
            'Como você avalia os equipamentos disponíveis?',
            'Como você avalia a biblioteca?',
            'Como você avalia o acesso a recursos digitais?',
            'Como você avalia a limpeza dos espaços?',
            'Como você avalia a segurança no campus?',
            'Como você avalia a acessibilidade?',
            'Como você avalia os espaços de convivência?',

            // 11–20
            'Como você avalia a qualidade do ensino?',
            'Como você avalia o corpo docente?',
            'Como você avalia os conteúdos das disciplinas?',
            'Como você avalia os métodos de avaliação?',
            'Como você avalia a organização do curso?',
            'Como você avalia a coordenação do curso?',
            'Como você avalia a comunicação institucional?',
            'Como você avalia os serviços acadêmicos?',
            'Como você avalia o atendimento administrativo?',
            'Como você avalia o suporte ao estudante?',

            // 21–30
            'Como você avalia as oportunidades de pesquisa?',
            'Como você avalia as atividades de extensão?',
            'Como você avalia as oportunidades de estágio?',
            'Como você avalia as ações de inclusão?',
            'Como você avalia as políticas de permanência?',
            'Você recomendaria a UEAP a outras pessoas?',
            'Você se sente satisfeito(a) com sua experiência na UEAP?',
            'O que a UEAP faz bem?',
            'O que a UEAP pode melhorar?',
            'Deseja deixar algum comentário adicional?'
        ];

        foreach ($questions as $index => $text) {

            // perguntas 26 e 27 → radio
            if (in_array($index + 1, [26, 27])) {
                $question = Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'text' => $text,
                    'type' => 'radio'
                ]);

                Option::insert([
                    ['question_id' => $question->id, 'text' => 'Sim'],
                    ['question_id' => $question->id, 'text' => 'Não']
                ]);

                continue;
            }

            // perguntas 28–30 → texto livre
            if ($index + 1 >= 28) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'text' => $text,
                    'type' => 'text'
                ]);

                continue;
            }

            // padrão → escala 1 a 5
            Question::create([
                'questionnaire_id' => $questionnaire->id,
                'text' => $text,
                'type' => 'scale'
            ]);
        }
    }
}
