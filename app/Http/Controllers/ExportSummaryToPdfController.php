<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ApplicationResource\Pages\ModelsFromFilter;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ExportSummaryToPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->missing('export_id')) {
            return redirect('/');
        }

        $exportId = $request->get('export_id');

        $filters = Cache::get("export_summary_to_pdf.{$exportId}");

        $models = new ModelsFromFilter($filters);

        $terms = $models->getTerms();
        $programs = $models->getPrograms();
        $statuses = $models->getStatuses();
        $recordsByStatuses = $models->getRecordsByStatuses();

        $pdf = SnappyPdf::loadView('exports.applications_summary_pdf', [
            'asOfLabel' => $asOfLabel = now()->format('m/d/Y H:i A'),
            'terms' => $terms,
            'programs' => $programs,
            'statuses' => $statuses,
            'recordsByStatuses' => $recordsByStatuses,
        ]);

        return $pdf
            ->setOrientation('landscape')
            ->download("Application Summary as of {$asOfLabel}.pdf");
    }
}
