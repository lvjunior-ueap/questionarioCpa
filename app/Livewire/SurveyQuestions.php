<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Audience;
use App\Models\Dimension;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\SurveySession;
use App\Support\DimensionTheme;
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
        $this->dispatchBackground();
    }

    public function submit()
    {
        $this->validate($this->rules(), $this->messages());

        $answersLimpos = $this->getAnswersLimpos();

        $anteriores = $this->getSavedAnswers();
        $respostas = $anteriores + $answersLimpos;

        $this->saveAnswers($respostas);

        if ($this->pagina < $this->totalPages) {
            return redirect()->to('/survey/' . ($this->pagina + 1));
        }

        $survey = Survey::where('active', true)->firstOrFail();

        $response = Response::create([
            'survey_id' => $survey->id,
            'audience_id' => $this->audienceId,
        ]);

        foreach ($respostas as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value' => $value,
            ]);
        }

        $this->markSessionAsCompleted();
        $this->clearSavedAnswers();
        $this->clearSeenDimensions();

        return redirect()->to('/finalizado');
    }

    public function rules(): array
    {
        if (! $this->currentQuestion) {
            return [];
        }

        $field = 'answers.' . $this->currentQuestion->id;
        $rules = [$field => ['required']];

        if ($this->currentQuestion->type === 'scale') {
            $rules[$field][] = 'integer';
            $rules[$field][] = 'between:1,5';
        }

        if ($this->currentQuestion->type === 'radio') {
            $allowed = $this->currentQuestion->options->pluck('text')->all();
            $rules[$field][] = 'in:' . implode(',', array_map(fn ($value) => str_replace(',', '\\,', $value), $allowed));
        }

        if ($this->currentQuestion->type === 'text') {
            $rules[$field][] = 'string';
            $rules[$field][] = 'min:3';
            $rules[$field][] = 'max:500';
        }

        return $rules;
    }

    public function messages(): array
    {
        if (! $this->currentQuestion) {
            return [];
        }

        $field = 'answers.' . $this->currentQuestion->id;

        return [
            $field . '.required' => 'Responda a pergunta para continuar.',
            $field . '.between' => 'Escolha um valor entre 1 e 5.',
            $field . '.in' => 'Escolha uma opção válida da lista.',
            $field . '.min' => 'A resposta deve ter pelo menos 3 caracteres.',
            $field . '.max' => 'A resposta deve ter no máximo 500 caracteres.',
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
        $this->dispatchBackground();
    }

    public function continueDimension(): void
    {
        if (! $this->currentQuestion?->dimension_id) {
            $this->showDimensionIntro = false;

            return;
        }

        $this->markDimensionAsSeen(
            (int) $this->currentQuestion->dimension_id
        );

        $this->showDimensionIntro = false;
    }

    private function loadCurrentAnswerFromSession(): void
    {
        if (! $this->currentQuestion) {
            return;
        }

        $savedAnswers = $this->getSavedAnswers();

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

    public function getResumeLinkProperty(): ?string
    {
        $token = session('token');

        if (! $token) {
            return null;
        }

        return route('survey.retomar', ['token' => $token]);
    }

    private function mergedAnswers(): array
    {
        $saved = $this->getSavedAnswers();

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
        $order = (int) ($this->currentQuestion?->dimension->order ?? 1);

        return app(DimensionTheme::class)->resolve($order);
    }

    public function getDimensionIntroTextProperty(): string
    {
        if ($this->dimensionDescription) {
            return $this->dimensionDescription;
        }

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

        $seen = $this->getSeenDimensions();

        return ! in_array($currentDimensionId, $seen, true);
    }

    private function getSavedAnswers(): array
    {
        return session('respostas', []);
    }

    private function saveAnswers(array $answers): void
    {
        session(['respostas' => $answers]);
        $this->persistSurveySession();
    }

    private function clearSavedAnswers(): void
    {
        session()->forget(['respostas', 'token']);
    }

    private function getSeenDimensions(): array
    {
        return session('dimension_intros_seen', []);
    }

    private function markDimensionAsSeen(int $dimensionId): void
    {
        $seen = $this->getSeenDimensions();
        $seen[] = $dimensionId;

        session([
            'dimension_intros_seen' => array_values(array_unique($seen)),
        ]);

        $this->persistSurveySession();
    }

    private function clearSeenDimensions(): void
    {
        session()->forget('dimension_intros_seen');
    }

    private function dispatchBackground(): void
    {
        $theme = $this->dimensionTheme;

        $this->dispatch('updateBackground', pattern: $theme['pattern']);
    }

    private function persistSurveySession(): void
    {
        $token = session('token');

        if (! $token) {
            return;
        }

        SurveySession::query()
            ->where('token', $token)
            ->whereNull('completed_at')
            ->update([
                'audience_id' => $this->audienceId,
                'answers' => $this->getSavedAnswers(),
                'seen_dimensions' => $this->getSeenDimensions(),
            ]);
    }

    private function markSessionAsCompleted(): void
    {
        $token = session('token');

        if (! $token) {
            return;
        }

        SurveySession::query()
            ->where('token', $token)
            ->whereNull('completed_at')
            ->update([
                'answers' => $this->getSavedAnswers(),
                'seen_dimensions' => $this->getSeenDimensions(),
                'completed_at' => now(),
            ]);
    }
}
