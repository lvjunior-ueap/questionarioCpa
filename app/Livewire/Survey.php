<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Survey as SurveyModel;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Support\Str;

class Survey extends Component
{
    public $step = 'perfil'; // perfil, survey, finalizado
    public $perfil = '';
    public $token = '';
    public $respostas = [];
    public $dimensions = [];
    public $currentIndex = 0;
    public $questions = [];
    public $currentDimension = '';
    public $total = 0;
    public $surveyId = null;

    public function mount()
    {
        // Se já tem sessão ativa, continua de onde parou
        if (session('perfil')) {
            $this->perfil = session('perfil');
            $this->token = session('token');
            $this->respostas = session('respostas', []);
            $this->step = 'survey';
            $this->loadSurveyData();
        }
    }

    public function salvarPerfil()
    {
        $this->validate([
            'perfil' => 'required'
        ]);

        $this->token = Str::uuid()->toString();
        $this->respostas = [];

        session([
            'perfil' => $this->perfil,
            'token' => $this->token,
            'respostas' => []
        ]);

        $this->step = 'survey';
        $this->loadSurveyData();
    }

    public function loadSurveyData()
    {
        $survey = SurveyModel::where('active', true)->firstOrFail();
        $this->surveyId = $survey->id;
        
        // Carrega dimensões ordenadas
        $this->dimensions = $survey->questions()
            ->select('dimension', 'dimension_order')
            ->groupBy('dimension', 'dimension_order')
            ->orderBy('dimension_order')
            ->get()
            ->values()
            ->toArray();

        $this->total = count($this->dimensions);
        $this->currentIndex = 0;
        
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        if (!isset($this->dimensions[$this->currentIndex])) {
            $this->step = 'finalizado';
            return;
        }

        $this->currentDimension = $this->dimensions[$this->currentIndex]['dimension'];

        $survey = SurveyModel::findOrFail($this->surveyId);
        
        $this->questions = $survey->questions()
            ->where('dimension', $this->currentDimension)
            ->where(function ($q) {
                $q->whereNull('target_perfil')
                  ->orWhere('target_perfil', $this->perfil);
            })
            ->with('options')
            ->get()
            ->toArray();
    }

    public function proximaPagina()
    {
        session(['respostas' => $this->respostas]);

        if ($this->currentIndex < $this->total - 1) {
            $this->currentIndex++;
            $this->loadQuestions();
        } else {
            $this->finalizarResposta();
        }
    }

    public function paginaAnterior()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadQuestions();
        }
    }

    public function finalizarResposta()
    {
        $response = Response::create([
            'survey_id' => $this->surveyId,
            'perfil' => $this->perfil
        ]);

        foreach ($this->respostas as $questionId => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'value' => $value
            ]);
        }

        session()->flush();
        $this->step = 'finalizado';
    }

    public function render()
    {
        return view('livewire.survey');
    }
}



