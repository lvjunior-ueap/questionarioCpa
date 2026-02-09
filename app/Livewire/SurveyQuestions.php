<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Livewire\Component;

class SurveyQuestions extends Component
{
    public int $pagina = 1;
    public int $totalPages = 3;
    public $questions;
    public array $answers = [];

    public function mount(int $pagina): void
    {
        $this->pagina = $pagina;
        $this->loadQuestions();

        $savedAnswers = session('respostas', []);
        foreach ($this->questions as $question) {
            if (array_key_exists($question->id, $savedAnswers)) {
                $this->answers[$question->id] = $savedAnswers[$question->id];
            }
        }
    }

    public function submit()
    {
        $this->validate($this->rules());

        $respostas = session('respostas', []);
        $respostas = array_merge($respostas, $this->answers);

        session(['respostas' => $respostas]);

        if ($this->pagina < $this->totalPages) {
            return redirect()->to('/survey/' . ($this->pagina + 1));
        }

        $survey = Survey::where('active', true)->firstOrFail();

        $response = Response::create([
            'survey_id' => $survey->id,
            'perfil' => session('perfil')
        ]);

        foreach ($respostas as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value' => $value
            ]);
        }

        session()->flush();

        return redirect()->to('/finalizado');
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->questions as $question) {
            $rules['answers.' . $question->id] = 'required';
        }

        return $rules;
    }

    private function loadQuestions(): void
    {
        $survey = Survey::where('active', true)->firstOrFail();

        $this->questions = Question::with('options')
            ->where('survey_id', $survey->id)
            ->orderBy('id')
            ->skip(($this->pagina - 1) * 10)
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.survey-questions');
    }
}
