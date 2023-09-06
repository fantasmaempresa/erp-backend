<?php

/*
 * OPEN2CODE 2023
 */
namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Shape;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\ArrayShape;
use Open2code\Pdf\jasper\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * SHAPE CONTROLLER
 */
class ShapeActionController extends ApiController
{

    /**
     * @param   Shape $shape
     * @return  BinaryFileResponse|JsonResponse
     * @throws  Exception
     */
    public function generateShape(Shape $shape): BinaryFileResponse|JsonResponse
    {
        $procedure = Procedure::find($shape->procedure->id);

        $procedure->user;
        $procedure->place;
        $procedure->client;
        $procedure->staff;
        $procedure->operation;
        $procedure->shape = $shape;
        $procedure->shape->signature_date_s = $this->separateDate(new DateTime($procedure->shape->signature_date));

        if ($shape->template_shape->id == 1) {
            $procedure->shape->alien_rfc_s = $this->splitString($procedure->shape->data_form['alienating_rfc'], 13, 'al_rfc');
            $procedure->shape->alien_curp_s = $this->splitString($procedure->shape->data_form['alienating_crup'], 18, 'al_curp');
            $procedure->shape->acq_rfc_s = $this->splitString($procedure->shape->data_form['acquirer_rfc'], 13, 'ac_rfc');
            $procedure->shape->acq_curp_s = $this->splitString($procedure->shape->data_form['acquirer_curp'], 18, 'ac_curp');

            $jasperPath = Storage::path('reports/format_1/FORMAT1.jasper');
            $outputPath = Storage::path('reports/format_1/FORMAT1.pdf');
        } else {
            $procedure->shape->rfc = $this->splitString($procedure->shape->data_form['rfc'], 13, 'rfc');
            $procedure->shape->curp = $this->splitString($procedure->shape->data_form['curp'], 18, 'curp');

            $jasperPath = Storage::path('reports/format_2/FORMAT2.jasper');
            $outputPath = Storage::path('reports/format_2/FORMAT2.pdf');
        }

        unset($procedure->shape['template_shape']);
        unset($procedure->shape['procedure']);

        $imageAsset = Storage::path('assets/LogoFinanzas.png');

        $pdf = new Report(
            $procedure,
            ['imageSF' => $imageAsset],
            $jasperPath,
            $outputPath
        );

        $result = $pdf->generateReport();

        return $result['success'] ? $this->downloadFile($outputPath) : $this->errorResponse($result['message'], 500);
    }

    /**
     * @param DateTime $date
     * @return array
     */
    #[ArrayShape(['year' => "string", 'month' => "string", 'day' => "string"])]
    private function separateDate(DateTime $date): array
    {
        return [
            'year' => $date->format("Y"),
            'month' => $date->format("m"),
            'day' => $date->format("d"),
        ];
    }

    /**
     * @param $input
     * @param $length
     * @param $prefix
     * @return array
     */
    private function splitString($input, $length, $prefix): array
    {
        $inputSplit = is_null($input) ? array_fill(0, $length, '') : str_split($input);

        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $result[$prefix.$i] = $inputSplit[$i] ?? '';
        }

        return $result;
    }
}
