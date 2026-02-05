<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionnaireController extends Controller
{
    /**
     * Página de definição de perfil
     */
    public function perfil()
    {
        return view('perfil');
    }

    /**
     * Salva perfil e gera token anônimo
     */
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

        return redirect('/questionario/1');
    }

    /**
     * Exibe perguntas paginadas (10 por página)
     */
    public function questionario($pagina)
    {
        $pagina = (int) $pagina;

        $questionnaire = Questionnaire::where('active', true)->firstOrFail();

        $questions = Question::with('options')
            ->where('questionnaire_id', $questionnaire->id)
            ->orderBy('id') // garante ordem das 30 perguntas
            ->skip(($pagina - 1) * 10)
            ->take(10)
            ->get();

        // segurança: se alguém tentar acessar página inválida
        if ($questions->isEmpty()) {
            return redirect('/finalizado');
        }

        return view('questionario', compact('questions', 'pagina'));
    }

    /**
     * Salva respostas da página atual
     * e persiste tudo ao final
     */
    public function salvarPagina(Request $request, $pagina)
    {
        $respostas = session('respostas', []);
        $respostas += $request->answers;

        session(['respostas' => $respostas]);

        if ($pagina < 3) {
            return redirect('/questionario/' . ($pagina + 1));
        }

        // Questionário ativo
        $questionnaire = Questionnaire::where('active', true)->firstOrFail();

        // Cria resposta final
        $response = Response::create([
            'questionnaire_id' => $questionnaire->id,
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
