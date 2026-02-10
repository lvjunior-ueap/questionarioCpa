<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RelatorioDataService
{
    /**
     * @return array<int, array{publico: string, total_perguntados: int}>
     */
    public function totaisPorPublico(): array
    {
        return DB::table('responses')
            ->join('audiences', 'audiences.id', '=', 'responses.audience_id')
            ->selectRaw('audiences.name as publico, COUNT(responses.id) as total_perguntados')
            ->groupBy('audiences.name')
            ->orderBy('audiences.name')
            ->get()
            ->map(fn ($row) => [
                'publico' => $row->publico,
                'total_perguntados' => (int) $row->total_perguntados,
            ])
            ->all();
    }

    /**
     * @return array<int, array{publico: string, dimensao: string, total_respostas: int}>
     */
    public function respostasPorDimensao(): array
    {
        return DB::table('answers')
            ->join('responses', 'responses.id', '=', 'answers.response_id')
            ->join('audiences', 'audiences.id', '=', 'responses.audience_id')
            ->join('questions', 'questions.id', '=', 'answers.question_id')
            ->join('dimensions', 'dimensions.id', '=', 'questions.dimension_id')
            ->selectRaw('audiences.name as publico, dimensions.title as dimensao, COUNT(answers.id) as total_respostas')
            ->groupBy('audiences.name', 'dimensions.title')
            ->orderBy('audiences.name')
            ->orderBy('dimensions.title')
            ->get()
            ->map(fn ($row) => [
                'publico' => $row->publico,
                'dimensao' => $row->dimensao,
                'total_respostas' => (int) $row->total_respostas,
            ])
            ->all();
    }

    /**
     * @return array<int, array{question_id:int, pergunta:string, resposta:string, total_respostas:int, percentual:string}>
     */
    public function respostasPorPerguntaComPercentuais(): array
    {
        $totaisPorPergunta = DB::table('answers')
            ->selectRaw('question_id, COUNT(*) as total')
            ->groupBy('question_id');

        return DB::table('questions')
            ->join('options', 'options.question_id', '=', 'questions.id')
            ->leftJoin('answers', function ($join) {
                $join->on('answers.question_id', '=', 'questions.id')
                    ->on('answers.value', '=', 'options.text');
            })
            ->leftJoinSub($totaisPorPergunta, 'totais', function ($join) {
                $join->on('totais.question_id', '=', 'questions.id');
            })
            ->selectRaw('questions.id as question_id, questions.text as pergunta, options.text as resposta, COUNT(answers.id) as total_respostas, COALESCE(totais.total, 0) as total_pergunta')
            ->groupBy('questions.id', 'questions.text', 'options.text', 'totais.total')
            ->orderBy('questions.id')
            ->orderBy('options.id')
            ->get()
            ->map(function ($row) {
                $totalRespostas = (int) $row->total_respostas;
                $totalPergunta = (int) $row->total_pergunta;
                $percentual = $totalPergunta > 0
                    ? number_format(($totalRespostas / $totalPergunta) * 100, 2, ',', '.') . '%'
                    : '0,00%';

                return [
                    'question_id' => (int) $row->question_id,
                    'pergunta' => $row->pergunta,
                    'resposta' => $row->resposta,
                    'total_respostas' => $totalRespostas,
                    'percentual' => $percentual,
                ];
            })
            ->all();
    }
}
