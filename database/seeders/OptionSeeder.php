<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Option;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'Não sei / Não se aplica',
            'Discordo totalmente',
            'Discordo parcialmente',
            'Indiferente',
            'Concordo parcialmente',
            'Concordo totalmente',
        ];

        Question::where('type', 'likert')->each(function ($question) use ($options) {
            foreach ($options as $index => $label) {
                Option::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'value' => $index,
                ]);
            }
        });
    }
}
