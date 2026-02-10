<?php

namespace App\Exports;

use App\Services\RelatorioDataService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RespostasPorDimensaoSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly RelatorioDataService $dataService)
    {
    }

    public function collection(): Collection
    {
        return collect($this->dataService->respostasPorDimensao())
            ->map(fn (array $row) => [
                $row['publico'],
                $row['dimensao'],
                $row['total_respostas'],
            ]);
    }

    public function headings(): array
    {
        return ['Público', 'Dimensão', 'Total de respostas'];
    }

    public function title(): string
    {
        return 'Respostas por dimensão';
    }
}
