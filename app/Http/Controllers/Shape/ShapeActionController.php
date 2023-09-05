<?php

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
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
        $procedure->shape->signature_date_s = $this->separateDate(new \DateTime($procedure->shape->signature_date));

        $procedure->shape->alien_rfc_s = $this->splitString($procedure->shape->data_form['alienating_rfc'], 13, 'al_rfc');
        $procedure->shape->alien_curp_s = $this->splitString($procedure->shape->data_form['alienating_crup'], 18, 'al_curp');
        $procedure->shape->acq_rfc_s = $this->splitString($procedure->shape->data_form['acquirer_rfc'], 13, 'ac_rfc');
        $procedure->shape->acq_curp_s = $this->splitString($procedure->shape->data_form['acquirer_curp'], 18, 'ac_curp');


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

    private function separateDate(\DateTime $date)
    {
        return [
            'year' => $date->format("Y"),
            'month' => $date->format("m"),
            'day' => $date->format("d"),
        ];
    }

    private function splitString($input, $length, $prefix)
    {
        $input_split = is_null($input) ? array_fill(0, $length, '') : str_split($input);

        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[$prefix . $i] = isset($input_split[$i]) ? $input_split[$i] : '';
        }

        return $result;
    }
}
