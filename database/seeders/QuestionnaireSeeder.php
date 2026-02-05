<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Option;

class QuestionnaireSeeder extends Seeder
{
    public function run()
    {
        $q = Questionnaire::create([
            'title' => 'Avaliação Institucional UEAP',
            'description' => 'Questionário da Comissão Própria de Avaliação'
        ]);

        $p1 = Question::create([
            'questionnaire_id' => $q->id,
            'text' => 'Como você avalia a infraestrutura?',
            'type' => 'scale'
        ]);

        $p2 = Question::create([
            'questionnaire_id' => $q->id,
            'text' => 'Você recomendaria a UEAP?',
            'type' => 'radio'
        ]);

        Option::insert([
            ['question_id' => $p2->id, 'text' => 'Sim'],
            ['question_id' => $p2->id, 'text' => 'Não']
        ]);
    }
}
