<?php

namespace App\Livewire;

use App\Models\Answer;
use App\Models\Audience;
use App\Models\Dimension;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Livewire\Component;

//tese
use Illuminate\Support\Facades\DB;

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

    /** 
     * answers SEMPRE será considerado um array “sujo”
     * (com buracos e nulls). Nunca use direto.
     */
    public array $answers = [];

    public function mount(int $pagina): void
    {
        $this->audienceId = session('audience_id');

        if (! $this->audienceId) {
            redirect()->to('/perfil')->send();
        }

        $this->pagina = $pagina;
        $this->loadQuestions();

        // Recarrega respostas já salvas da sessão (apenas da dimensão atual)
        $savedAnswers = session('respostas', []);

        foreach ($this->questions as $question) {
            if (array_key_exists($question->id, $savedAnswers)) {
                $this->answers[$question->id] = $savedAnswers[$question->id];
            }
        }
    }

    public function submit()
    {

        // Valida apenas as perguntas da dimensão atual
        $this->validate($this->rules());

        // 🔑 LIMPEZA CRÍTICA
        $answersLimpos = $this->getAnswersLimpos();

        // Acumula na sessão preservando as chaves (question_id)
        $anteriores = session('respostas', []);
        $respostas  = $anteriores + $answersLimpos;

        session(['respostas' => $respostas]);

        // Próxima dimensão
        if ($this->pagina < $this->totalPages) {
            return redirect()->to('/survey/' . ($this->pagina + 1));
        }

        // Última página → persistir no banco
        $survey = Survey::where('active', true)->firstOrFail();


        $response = Response::create([
            'survey_id'   => $survey->id,
            'audience_id' => $this->audienceId,
        ]);



        // Salva TODAS as respostas acumuladas
        foreach ($respostas as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value'       => $value,
            ]);
        }

//vamos ver...
        DB::table('answers')->insert([
            'response_id' => $response->id,
            'question_id' => array_key_first($respostas),
            'value'       => reset($respostas),
        ]);
        
        

        // (Opcional) normalização 0…5
        $mapa = [
            'Não sei / Não se aplica' => 0,
            'Discordo totalmente'     => 1,
            'Discordo parcialmente'   => 2,
            'Indiferente'             => 3,
            'Concordo parcialmente'   => 4,
            'Concordo totalmente'     => 5,
        ];

        $valoresNormalizados = [];
        foreach ($respostas as $questionId => $texto) {
            $valoresNormalizados[$questionId] = $mapa[$texto] ?? null;
        }












        // Limpa sessão ao final
        //session()->forget('respostas');










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

    public function updatedAnswers(): void
    {
        // força re-render quando qualquer resposta mudar
    }



    /**
     * Retorna apenas respostas válidas (sem nulls)
     */
    private function getAnswersLimpos(): array
    {
        return array_filter(
            $this->answers,
            fn ($value) => $value !== null
        );
    }

    private function loadQuestions(): void
    {
        // Limpa respostas da dimensão anterior
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

        $this->totalPages = $this->dimensions->count();
        $currentDimension = $this->dimensions->get($this->pagina - 1);

        if (! $currentDimension) {
            redirect()->to('/survey/1')->send();
        }

        $this->dimensionTitle = $currentDimension->title;
        $this->dimensionDescription = $currentDimension->description;

        $this->questions = Question::with('options')
            ->where('survey_id', $survey->id)
            ->where('dimension_id', $currentDimension->id)
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.survey-questions');
    }


    // barra 1
    public function getTotalPerguntasProperty(): int
    {
        return count($this->questions ?? []);
    }
    
    public function getRespondidasProperty(): int
    {
        return count(array_filter($this->answers, fn ($v) => !empty($v)));
    }
        
    public function getProgressoProperty(): int
    {
        $total = count($this->questions ?? []);

        if ($total === 0) {
            return 0;
        }

        $respondidas = 0;

        foreach ($this->answers as $value) {
            if (!empty($value)) {
                $respondidas++;
            }
        }

        return (int) round(($respondidas / $total) * 100);
    }

    
    // barra 2
    public function getProgressoDimensaoProperty(): int
    {
        if ($this->totalPages === 0) {
            return 0;
        }

        return (int) round(($this->pagina / $this->totalPages) * 100);
    }

 

}
