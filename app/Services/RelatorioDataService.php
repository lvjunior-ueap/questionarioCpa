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
}
