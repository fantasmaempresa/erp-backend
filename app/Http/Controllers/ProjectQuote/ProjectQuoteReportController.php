<?php
/*
 * CODE
 * ProjectQuote Controller
*/
namespace App\Http\Controllers\ProjectQuote;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Open2code\Pdf\jasper\Report;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuoteReportController extends ApiController
{
    /**
     * @param  Request $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function makePDF(Request $request): BinaryFileResponse|JsonResponse
    {
        $imageAsset = Storage::path('assets/LogoNotaria.png');
        $subReportPath = Storage::path('reports/quotation/');

        $jasperPath = Storage::path('reports/quotation/COTIZACION.jasper');
        $outputPath = Storage::path('reports/quotation/quotation.pdf');

        $pdf = new Report(
            $request->all(),
            ['Logo' => $imageAsset, 'subReportPath' => $subReportPath],
            $jasperPath,
            $outputPath
        );

        $result = $pdf->generateReport();

        return $result['success'] ? $this->downloadFile($outputPath) : $this->errorResponse($result['message'], 500);
    }
}