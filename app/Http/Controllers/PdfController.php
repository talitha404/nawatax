<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    /**
     * Render the already-calculated calculator state. This endpoint deliberately
     * does not invoke the calculator engine: the PDF must reflect the exact
     * values the user saw before downloading it.
     */
    public function generate(Request $request): Response
    {
        $data = $request->validate([
            'input_summary' => ['required', 'array'],
            'calculation_result' => ['required', 'array'],
            'breakdown_detail' => ['required', 'array'],
        ]);

        $logoPath = public_path('nawatax.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.report', [
            'inputSummary' => $data['input_summary'],
            'calculationResult' => $data['calculation_result'],
            'breakdownDetail' => $data['breakdown_detail'],
            'generatedAt' => now()->locale('id')->translatedFormat('d F Y'),
            'logoDataUri' => $logoDataUri,
            'printCss' => (string) file_get_contents(resource_path('css/print.css')),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('nawatax-report-'.now()->format('Ymd-His').'.pdf');
    }
}
