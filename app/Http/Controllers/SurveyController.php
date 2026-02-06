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

    public function survey($pagina)
    {
        $pagina = (int) $pagina;

        $survey = Survey::where('active', true)->firstOrFail();

        $questions = Question::with('options')
            ->where('survey_id', $survey->id)
            ->orderBy('id')
            ->skip(($pagina - 1) * 10)
            ->take(10)
            ->get();

        return view('survey', compact('questions', 'pagina'));
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

        return redirect('/finalizado');
    }
}
