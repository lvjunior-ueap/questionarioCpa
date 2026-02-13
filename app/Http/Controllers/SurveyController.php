<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function perfil()
    {
        $audiences = Audience::orderBy('name')->get();

        $audienceDescriptions = [
            'discente' => 'Discente: estudante regularmente matriculado(a) em cursos da UEAP.',
            'docente' => 'Docente: professor(a) que atua em atividades de ensino, pesquisa e/ou extensão na UEAP.',
            'egresso' => 'Egresso: ex-estudante que já concluiu curso na UEAP.',
            'tecnico' => 'Técnico Administrativo: servidor(a) técnico(a)-administrativo(a) da instituição.',
            'externo' => 'Comunidade Externa: pessoa sem vínculo direto com a UEAP, mas que interage com suas ações e serviços.',
        ];

        return view('perfil', compact('audiences', 'audienceDescriptions'));
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'audience_id' => 'required|exists:audiences,id'
        ]);

        session([
            'audience_id' => (int) $request->audience_id,
            'token' => Str::uuid()->toString(),
            'respostas' => []
        ]);

        return redirect('/survey/1');
    }

    public function survey($pagina)
    {
        $pagina = (int) $pagina;
        return view('survey', compact('pagina'));
    }
}
