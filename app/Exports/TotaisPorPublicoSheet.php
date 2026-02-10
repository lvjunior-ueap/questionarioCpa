<?php

namespace App\Exports;

use App\Services\RelatorioDataService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TotaisPorPublicoSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly RelatorioDataService $dataService)
    {
    }

    public function collection(): Collection
    {
        return collect($this->dataService->totaisPorPublico())
            ->map(fn (array $row) => [
                $row['publico'],
                $row['total_perguntados'],
            ]);
    }

    public function headings(): array
    {
        return ['Público', 'Total de perguntados'];
    }

    public function title(): string
    {
        return 'Perguntados por público';
    }
}
