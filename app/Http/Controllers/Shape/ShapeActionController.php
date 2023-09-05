<?php

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;
use Open2code\Pdf\jasper\Report;
use Illuminate\Support\Facades\Storage;

class ShapeActionController extends ApiController
{

    /**
     * @param Procedure $procedure
     * @return JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateShape(Procedure $procedure)
    {
        $procedure->user;
        $procedure->place;
        $procedure->client;
        $procedure->staff;
        $procedure->operation;
        $procedure->shape = $procedure->shapes()->where('template_shape_id', 1)->first();

        $outputPath = Storage::path('reports/format_1/FORMAT1.pdf');
        $imageAsset = Storage::path('assets/LogoFinanzas.png');

        $pdf = new Report(
            $procedure,
            ['imageSF' => $imageAsset],
            Storage::path('reports/format_1/FORMAT1.jasper'),
            $outputPath
        );

        $result = $pdf->generateReport();

        if ($result['success']) {
            return $this->downloadFile($outputPath);
        } else {
            return $this->errorResponse($result['message'], 500);
        }
    }

    private function separateDate($date){

    }
}
