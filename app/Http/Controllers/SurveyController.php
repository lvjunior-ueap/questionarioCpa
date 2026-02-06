<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function perfil()
    {
        return view('perfil');
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'perfil' => 'required'
        ]);

        session([
            'perfil' => $request->perfil,
            'token' => Str::uuid()->toString(),
            'respostas' => []
        ]);

        return redirect('/survey/1');
    }

    public function survey($index)
    {
        $survey = Survey::where('active', true)->firstOrFail();

        $perfil = session('perfil');
    
        // lista ordenada de dimensões
        $dimensions = $survey->questions()
        ->select('dimension', 'dimension_order')
        ->groupBy('dimension', 'dimension_order')
        ->orderBy('dimension_order')
        ->get()
        ->values();

    
        $index = (int) $index;
    
        if (!isset($dimensions[$index])) {
            return redirect('/finalizado');
        }
    
        $currentDimension = $dimensions[$index]->dimension;
    
        $questions = $survey->questions()
        ->where('dimension', $currentDimension)
        ->where(function ($q) use ($perfil) {
            $q->whereNull('target_perfil')
            ->orWhere('target_perfil', $perfil);
        })
        ->with('options')
        ->get();
        
        return view('survey', [
            'questions' => $questions,
            'dimension' => $currentDimension,
            'index' => $index,
            'total' => $dimensions->count(),
        ]);
    }

    public function salvarPagina(Request $request, $pagina)
    {
        $respostas = session('respostas', []);
        $respostas += $request->answers;

        session(['respostas' => $respostas]);

        if ($pagina < 3) {
            return redirect('/survey/' . ($pagina + 1));
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

        return redirect('/survey/' . ($pagina + 1));
        
    }
}
