<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveySession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function perfil()
    {
        $this->ensurePerfilAudiences();

        $audiences = Audience::orderBy('name')->get();

        $audienceDescriptions = [
            'discente' => 'Discente: estudante regularmente matriculado(a) em cursos da UEAP.',
            'docente' => 'Docente: professor(a) que atua em atividades de ensino, pesquisa e/ou extensão na UEAP.',
            'egresso' => 'Egresso: ex-estudante que já concluiu curso na UEAP.',
            'tecnico' => 'Técnico Administrativo: servidor(a) técnico(a)-administrativo(a) da instituição.',
            'externo' => 'Comunidade Externa: pessoa sem vínculo direto com a UEAP, mas que interage com suas ações e serviços.',
            'gestao' => 'Gestão da UEAP: servidor(a) que atua hoje ou já atuou em funções de gestão institucional.',
            'alta_gestao' => 'Reitor, Diretor ou Pró-Reitor: ocupante de cargo de alta liderança acadêmico-administrativa.',
            'transposto' => 'Funcionário Transposto: servidor(a) aposentado(a) que foi reconvocado(a) para atuação profissional na UEAP.',
        ];

        $activeSurveyId = Survey::query()->where('active', true)->value('id');

        $audienceStats = [];
        if ($activeSurveyId) {
            foreach ($audiences as $audience) {
                $dimensionCount = $audience->dimensions()
                    ->where('survey_id', $activeSurveyId)
                    ->count();

                $questionCount = Question::query()
                    ->where('survey_id', $activeSurveyId)
                    ->whereIn(
                        'dimension_id',
                        $audience->dimensions()->where('survey_id', $activeSurveyId)->select('id')
                    )
                    ->count();

                $audienceStats[$audience->id] = [
                    'dimensions' => $dimensionCount,
                    'questions' => $questionCount,
                    'estimated_minutes' => max(3, (int) ceil($questionCount / 12)),
                ];
            }
        }

        return view('perfil', compact('audiences', 'audienceDescriptions', 'audienceStats'));
    }

    public function salvarPerfil(Request $request)
    {
        $request->validate([
            'audience_id' => 'required|exists:audiences,id',
        ]);

        $token = Str::uuid()->toString();

        SurveySession::create([
            'token' => $token,
            'audience_id' => (int) $request->audience_id,
            'answers' => [],
            'seen_dimensions' => [],
        ]);

        session([
            'audience_id' => (int) $request->audience_id,
            'token' => $token,
            'respostas' => [],
            'dimension_intros_seen' => [],
        ]);

        return redirect('/survey/1');
    }

    public function retomar(string $token)
    {
        $surveySession = SurveySession::query()
            ->where('token', $token)
            ->whereNull('completed_at')
            ->firstOrFail();

        session([
            'audience_id' => $surveySession->audience_id,
            'token' => $surveySession->token,
            'respostas' => $surveySession->answers ?? [],
            'dimension_intros_seen' => $surveySession->seen_dimensions ?? [],
        ]);

        return redirect('/survey/1');
    }



    private function ensurePerfilAudiences(): void
    {
        $audiences = [
            'docente' => 'Docente',
            'discente' => 'Discente',
            'tecnico' => 'Técnico Administrativo',
            'egresso' => 'Egresso',
            'externo' => 'Comunidade Externa',
            'gestao' => 'Funcionário da Gestão da UEAP (atual ou ex-gestão)',
            'alta_gestao' => 'Reitor, Diretor ou Pró-Reitor',
            'transposto' => 'Funcionário Transposto',
        ];

        foreach ($audiences as $slug => $name) {
            Audience::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'intro_text' => "Questionário destinado a {$name}.",
                ]
            );
        }
    }

    public function survey($pagina)
    {
        $pagina = (int) $pagina;

        return view('survey', compact('pagina'));
    }
}
