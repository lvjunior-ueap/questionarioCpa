<?php

namespace App\Exports;

use App\Services\RelatorioDataService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RelatorioCpaExport implements WithMultipleSheets
{
    public function __construct(private readonly RelatorioDataService $dataService)
    {
    }

    public function sheets(): array
    {
        return [
            new TotaisPorPublicoSheet($this->dataService),
            new RespostasPorDimensaoSheet($this->dataService),
        ];
    }
}
