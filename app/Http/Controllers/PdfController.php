<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $data = $request->validate([
                'input_summary' => ['required', 'array'],
                'calculation_result' => ['required', 'array'],
                'breakdown_detail' => ['required', 'array'],
            ]);

            Log::info('PDF REQUEST:', $data);

            $logoPath = public_path('nawatax.png');
            $logoDataUri = is_file($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null;

            $cssPath = resource_path('css/print.css');
            $printCss = is_file($cssPath)
                ? file_get_contents($cssPath)
                : '';

            $pdf = Pdf::loadView('pdf.report', [
                'inputSummary' => $data['input_summary'],
                'calculationResult' => $data['calculation_result'],
                'breakdownDetail' => $data['breakdown_detail'],
                'generatedAt' => now()->locale('id')->translatedFormat('d F Y'),
                'logoDataUri' => $logoDataUri,
                'printCss' => $printCss,
            ])->setPaper('a4', 'portrait');

            return $pdf->download('nawatax-report-' . now()->format('Ymd-His') . '.pdf');

        } catch (\Throwable $e) {
            Log::error('PDF ERROR: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
