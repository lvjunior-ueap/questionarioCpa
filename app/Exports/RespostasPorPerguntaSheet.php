<?php

namespace App\Exports;

use App\Services\RelatorioDataService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RespostasPorPerguntaSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly RelatorioDataService $dataService)
    {
    }

    public function collection(): Collection
    {
        return collect($this->dataService->respostasPorPerguntaComPercentuais())
            ->map(fn (array $row) => [
                $row['question_id'],
                $row['pergunta'],
                $row['resposta'],
                $row['total_respostas'],
                $row['percentual'],
            ]);
    }

    public function headings(): array
    {
        return ['ID da pergunta', 'Pergunta', 'Resposta possível', 'Total de respostas', 'Percentual'];
    }

    public function title(): string
    {
        return 'Respostas por pergunta';
    }
}
