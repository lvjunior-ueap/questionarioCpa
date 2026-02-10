<?php

namespace App\Http\Controllers;

use App\Exports\RelatorioCpaExport;
use App\Services\RelatorioDataService;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioController extends Controller
{
    public function __invoke(RelatorioDataService $dataService)
    {
        return Excel::download(
            new RelatorioCpaExport($dataService),
            'relatorio-cpa-' . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
