<?php

/*
 * OPEN2CODE 2023
 */

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Shape;
use App\Models\Stake;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param Shape $shape
     * @param Request $request
     * @return BinaryFileResponse|JsonResponse
     * @throws Exception
     */
    public function generateShape(Shape $shape, Request $request): BinaryFileResponse|JsonResponse
    {
        $procedure = Procedure::find($shape->procedure->id);

        $procedure->operation;
        $procedure->shape = $shape;
        $procedure->shape->signature_date_s = $this->separateDate(new DateTime($procedure->shape->signature_date));

//        return $this->showList($procedure);
        //ALIENATING DATA
        $alienating = $procedure->grantors()->where('stake_id', Stake::ALIENATING)->get();
        $procedure->shape->alienatingData = [
            "name" => ($shape->id > 7461) ? $alienating[0]['name'] : $procedure->shape->data_form['alienating_name'],
            "street" => ($shape->id > 7461) ? $alienating[0]['name'] : $procedure->shape->data_form['alienating_street'],
            "outdoor_number" => ($shape->id > 7461) ? $alienating[0]['no_ext'] : $procedure->shape->data_form['alienating_outdoor_number'],
            "interior_number" => ($shape->id > 7461) ? $alienating[0]['no_int'] : $procedure->shape->data_form['alienating_interior_number'],
            "colony" => ($shape->id > 7461) ? $alienating[0]['colony'] : $procedure->shape->data_form['alienating_colony'],
            "locality" => ($shape->id > 7461) ? $alienating[0]['locality'] : $procedure->shape->data_form['alienating_locality'],
            "municipality" => ($shape->id > 7461) ? $alienating[0]['municipality'] : $procedure->shape->data_form['alienating_municipality'],
            "entity" => ($shape->id > 7461) ? $alienating[0]['municipality'] : $procedure->shape->data_form['alienating_entity'],
            "zipcode" => ($shape->id > 7461) ? $alienating[0]['zipcode'] : $procedure->shape->data_form['alienating_zipcode'],
            "phone" => ($shape->id > 7461) ? $alienating[0]['phone'] : $procedure->shape->data_form['alienating_phone'],
        ];

        //ACQUIRER DATA
        $acquirer = $procedure->grantors()->where('stake_id', Stake::ACQUIRER)->get();
        $procedure->shape->acquirerData = [
            "name" => ($shape->id > 7461) ? $acquirer[0]['name'] : $procedure->shape->data_form['acquirer_name'],
            "street" => ($shape->id > 7461) ? $acquirer[0]['name'] : $procedure->shape->data_form['acquirer_street'],
            "outdoor_number" => ($shape->id > 7461) ? $acquirer[0]['no_ext'] : $procedure->shape->data_form['acquirer_outdoor_number'],
            "interior_number" => ($shape->id > 7461) ? $acquirer[0]['no_int'] : $procedure->shape->data_form['acquirer_interior_number'],
            "colony" => ($shape->id > 7461) ? $acquirer[0]['colony'] : $procedure->shape->data_form['acquirer_colony'],
            "locality" => ($shape->id > 7461) ? $acquirer[0]['locality'] : $procedure->shape->data_form['acquirer_locality'],
            "municipality" => ($shape->id > 7461) ? $acquirer[0]['municipality'] : $procedure->shape->data_form['acquirer_municipality'],
            "entity" => ($shape->id > 7461) ? $acquirer[0]['municipality'] : $procedure->shape->data_form['acquirer_entity'],
            "zipcode" => ($shape->id > 7461) ? $acquirer[0]['zipcode'] : $procedure->shape->data_form['acquirer_zipcode'],
            "phone" => ($shape->id > 7461) ? $acquirer[0]['phone'] : $procedure->shape->data_form['acquirer_phone'],
        ];

        if ($shape->template_shape->id == 1) {
            $procedure->shape->alien_rfc_s = $this->splitString(
                ($shape->id > 7461) ? $alienating[0]['rfc'] : $procedure->shape->data_form['alienating_rfc'],
                13,
                'al_rfc'
            );
            $procedure->shape->alien_curp_s = $this->splitString(
                ($shape->id > 7461) ? $alienating[0]['curp'] : $procedure->shape->data_form['alienating_crup'],
                18,
                'al_curp'
            );
            $procedure->shape->acq_rfc_s = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['rfc'] : $procedure->shape->data_form['acquirer_rfc'],
                13,
                'ac_rfc'
            );
            $procedure->shape->acq_curp_s = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['curp'] : $procedure->shape->data_form['acquirer_curp'],
                18,
                'ac_curp'
            );

            $jasperPath = Storage::path('reports/format_1/FORMAT1.jasper');
            $outputPath = Storage::path('reports/format_1/FORMAT1.pdf');
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == 2) {
            $procedure->shape->rfc = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['rfc'] : $procedure->shape->data_form['rfc'],
                13,
                'rfc'
            );
            $procedure->shape->curp = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['curp'] : $procedure->shape->data_form['curp'],
                18,
                'curp'
            );

            $jasperPath = Storage::path('reports/format_2/FORMAT2.jasper');
            $outputPath = Storage::path('reports/format_2/FORMAT2.pdf');
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == 3){
            $procedure->shape->rfc = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['rfc'] : $procedure->shape->data_form['rfc'],
                13,
                'rfc'
            );
            $procedure->shape->curp = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['curp'] : $procedure->shape->data_form['curp'],
                18,
                'curp'
            );

            $jasperPath = Storage::path('reports/format_c/FORMAT_C.jasper');
            $outputPath = Storage::path('reports/format_c/FORMAT_C.pdf');
            $imageAsset = Storage::path('assets/LogoFormaC.png');
        } elseif ($shape->template_shape->id == 4) {
            $procedure->shape->rfc = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['rfc'] : $procedure->shape->data_form['rfc'],
                13,
                'rfc'
            );
            $procedure->shape->curp = $this->splitString(
                ($shape->id > 7461) ? $acquirer[0]['curp'] : $procedure->shape->data_form['curp'],
                18,
                'curp'
            );

            $jasperPath = Storage::path('reports/format_t/FORMAT_T.jasper');
            $outputPath = Storage::path('reports/format_t/FORMAT_T.pdf');
            $imageAsset = Storage::path('assets/LogoFormaT.png');
        }

        unset($procedure->shape['template_shape']);
        unset($procedure->shape['procedure']);

        $jsonData = json_encode($procedure);

        Storage::put("reports/tempJson.json", $jsonData);

        $pdf = new Report(
            Storage::path('reports/tempJson.json'),
            ['imageSF' => $imageAsset],
            $jasperPath,
            $outputPath
        );

        $result = $pdf->generateReport();

        Storage::delete("reports/tempJson.json");

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
