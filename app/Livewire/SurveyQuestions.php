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
    public bool $showDimensionIntro = false;

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

        $this->showDimensionIntro = $this->shouldShowDimensionIntro();
    }

    public function continueDimension(): void
    {
        if (! $this->currentQuestion?->dimension_id) {
            $this->showDimensionIntro = false;

            return;
        }

        $seen = session('dimension_intros_seen', []);
        $seen[] = $this->currentQuestion->dimension_id;

        session(['dimension_intros_seen' => array_values(array_unique($seen))]);

        $this->showDimensionIntro = false;
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


    public function getPaginaAnteriorUrlProperty(): ?string
    {
        if ($this->pagina <= 1) {
            return null;
        }

        return '/survey/' . ($this->pagina - 1);
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

    public function getDimensionThemeProperty(): array
    {
        if (! $this->currentQuestion?->dimension) {
            return $this->buildTheme('#2563eb', '🧭');
        }

        $themes = [
            1 => ['#3b82f6', '🧭'],
            2 => ['#8b5cf6', '📘'],
            3 => ['#14b8a6', '🧑‍🏫'],
            4 => ['#f97316', '👥'],
            5 => ['#0ea5e9', '🧩'],
            6 => ['#6366f1', '🏛️'],
            7 => ['#10b981', '🏫'],
            8 => ['#f59e0b', '📊'],
            9 => ['#ec4899', '🎓'],
            10 => ['#6b7280', '💰'],
        ];

        $order = (int) ($this->currentQuestion->dimension->order ?? 1);
        $selected = $themes[$order] ?? ['#2563eb', '🧭'];

        return $this->buildTheme($selected[0], $selected[1]);
    }

    public function getDimensionIntroTextProperty(): string
    {
        if (! $this->dimensionTitle) {
            return 'Vamos para a próxima etapa do questionário.';
        }

        return 'Agora você responderá perguntas sobre ' . mb_strtolower($this->dimensionTitle) . '.';
    }

    private function shouldShowDimensionIntro(): bool
    {
        if (! $this->currentQuestion?->dimension_id) {
            return false;
        }

        $currentDimensionId = (int) $this->currentQuestion->dimension_id;
        $previousQuestion = $this->questions->get($this->pagina - 2);
        $isNewDimension = ! $previousQuestion || (int) $previousQuestion->dimension_id !== $currentDimensionId;

        if (! $isNewDimension) {
            return false;
        }

        $seen = session('dimension_intros_seen', []);

        return ! in_array($currentDimensionId, $seen, true);
    }

    private function buildTheme(string $primaryColor, string $emoji): array
    {
        $encodedEmoji = rawurlencode($emoji);

        return [
            'primary' => $primaryColor,
            'soft' => $primaryColor . '1A',
            'pattern' => "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='92' height='92' viewBox='0 0 92 92'%3E%3Cg transform='rotate(60 46 46)'%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-size='28' fill='%23d1d5db'%3E{$encodedEmoji}%3C/text%3E%3C/g%3E%3C/svg%3E\")",
        ];
    }
}
