<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Audience;
use App\Models\Dimension;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Livewire\Component;

class SurveyQuestions extends Component
{
    public int $pagina = 1;
    public int $totalPages = 0;

    public ?int $audienceId = null;
    public ?string $audienceIntro = null;
    public ?string $dimensionTitle = null;
    public ?string $dimensionDescription = null;

    public $questions;
    public $dimensions;
    public ?Question $currentQuestion = null;

    public array $answers = [];

    public function mount(int $pagina): void
    {
        $this->audienceId = session('audience_id');

        if (! $this->audienceId) {
            redirect()->to('/perfil')->send();
        }

        $this->pagina = max(1, $pagina);

        $this->loadSurveyData();
        $this->loadCurrentQuestion();
        $this->loadCurrentAnswerFromSession();
    }

    public function submit()
    {
        $this->validate($this->rules());

        $answersLimpos = $this->getAnswersLimpos();

        $anteriores = session('respostas', []);
        $respostas  = $anteriores + $answersLimpos;

        session(['respostas' => $respostas]);

        if ($this->pagina < $this->totalPages) {
            return redirect()->to('/survey/' . ($this->pagina + 1));
        }

        $survey = Survey::where('active', true)->firstOrFail();

        $response = Response::create([
            'survey_id'   => $survey->id,
            'audience_id' => $this->audienceId,
        ]);

        foreach ($respostas as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value'       => $value,
            ]);
        }

        session()->forget('respostas');

        return redirect()->to('/finalizado');
    }

    public function rules(): array
    {
        if (! $this->currentQuestion) {
            return [];
        }

        return [
            'answers.' . $this->currentQuestion->id => 'required',
        ];
    }

    public function updatedAnswers($value, $key): void
    {
        $this->answers[$key] = $value;
    }

    private function getAnswersLimpos(): array
    {
        return array_filter(
            $this->answers,
            fn ($value) => $value !== null && (! is_string($value) || trim($value) !== '')
        );
    }

    private function loadSurveyData(): void
    {
        $this->answers = [];

        $survey = Survey::where('active', true)->firstOrFail();

        $audience = Audience::findOrFail($this->audienceId);
        $this->audienceIntro = $audience->intro_text;

        $this->dimensions = Dimension::query()
            ->where('survey_id', $survey->id)
            ->where('audience_id', $this->audienceId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $dimensionOrderMap = $this->dimensions->pluck('order', 'id');

        $this->questions = Question::with(['options', 'dimension'])
            ->where('survey_id', $survey->id)
            ->whereIn('dimension_id', $this->dimensions->pluck('id'))
            ->orderBy('id')
            ->get()
            ->sortBy(fn ($question) => [
                (int) ($dimensionOrderMap[$question->dimension_id] ?? PHP_INT_MAX),
                (int) $question->id,
            ])
            ->values();

        $this->totalPages = $this->questions->count();

        if ($this->totalPages === 0) {
            redirect()->to('/finalizado')->send();
        }

        if ($this->pagina > $this->totalPages) {
            redirect()->to('/survey/1')->send();
        }
    }

    private function loadCurrentQuestion(): void
    {
        $this->currentQuestion = $this->questions->get($this->pagina - 1);

        if (! $this->currentQuestion) {
            redirect()->to('/survey/1')->send();
        }

        $this->dimensionTitle = $this->currentQuestion->dimension?->title;
        $this->dimensionDescription = $this->currentQuestion->dimension?->description;
    }

    private function loadCurrentAnswerFromSession(): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        $savedAnswers = session('respostas', []);

        if (array_key_exists($this->currentQuestion->id, $savedAnswers)) {
            $this->answers[$this->currentQuestion->id] = $savedAnswers[$this->currentQuestion->id];
        }
    }

    public function render()
    {
        return view('livewire.survey-questions');
    }

    public function getTotalPerguntasProperty(): int
    {
        return count($this->questions ?? []);
    }

    public function getRespondidasProperty(): int
    {
        return count(array_filter($this->mergedAnswers(), fn ($value) => $this->isAnswered($value)));
    }

    public function getProgressoProperty(): int
    {
        if ($this->totalPerguntas === 0) {
            return 0;
        }

        return (int) round(($this->respondidas / $this->totalPerguntas) * 100);
    }

    public function getProgressoDimensaoProperty(): int
    {
        $totalDimensao = $this->totalPerguntasDimensaoAtual;

        if ($totalDimensao === 0) {
            return 0;
        }

        return (int) round(($this->respondidasDimensaoAtual / $totalDimensao) * 100);
    }

    public function getIndiceDimensaoAtualProperty(): int
    {
        if (! $this->currentQuestion || ! $this->currentQuestion->dimension_id) {
            return 0;
        }

        $index = $this->dimensions
            ?->search(fn ($dimension) => (int) $dimension->id === (int) $this->currentQuestion->dimension_id);

        if ($index === false) {
            return 0;
        }

        return $index + 1;
    }

    public function getTotalDimensoesProperty(): int
    {
        return count($this->dimensions ?? []);
    }

    public function getTotalPerguntasDimensaoAtualProperty(): int
    {
        if (! $this->currentQuestion) {
            return 0;
        }

        return $this->questions
            ->where('dimension_id', $this->currentQuestion->dimension_id)
            ->count();
    }

    public function getRespondidasDimensaoAtualProperty(): int
    {
        if (! $this->currentQuestion) {
            return 0;
        }

        $questionIds = $this->questions
            ->where('dimension_id', $this->currentQuestion->dimension_id)
            ->pluck('id')
            ->all();

        $mergedAnswers = $this->mergedAnswers();

        $count = 0;
        foreach ($questionIds as $questionId) {
            if (isset($mergedAnswers[$questionId]) && $this->isAnswered($mergedAnswers[$questionId])) {
                $count++;
            }
        }

        return $count;
    }

    private function mergedAnswers(): array
    {
        $saved = session('respostas', []);

        return $saved + $this->getAnswersLimpos();
    }

    private function isAnswered(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        return $value !== null;
    }
}
