<?php

/*
 * OPEN2CODE 2023
 */

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Shape;
use App\Models\Stake;
use App\Models\TemplateShape;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Open2code\Pdf\jasper\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function PHPSTORM_META\type;

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
    public function generateShape(Shape $shape): BinaryFileResponse|JsonResponse
    {
        $procedure = Procedure::find($shape->procedure->id);

        $procedure->operation;
        $procedure->shape = $shape;
        $procedure->shape->signature_date_s = $this->separateDate(new DateTime($procedure->shape->signature_date));

        $parameters = [];

        if ($shape->template_shape->id == TemplateShape::FORM01) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM01);

            $parameters += ['subReportPath' => Storage::path('reports/format_1/')];

            $jasperPath = Storage::path('reports/format_1/FORMAT1.jasper');
            $outputPath = Storage::path('reports/format_1/FORMAT1.pdf');
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM02) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM02);

            $parameters += ['subReportPath' => Storage::path('reports/format_2/')];

            $jasperPath = Storage::path('reports/format_2/FORMAT2.jasper');
            $outputPath = Storage::path('reports/format_2/FORMAT2.pdf');
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM03) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM03);

            $jasperPath = Storage::path('reports/format_c/FORMAT_C.jasper');
            $outputPath = Storage::path('reports/format_c/FORMAT_C.pdf');
            $imageAsset = Storage::path('assets/LogoFormaC.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM04) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM04);

            $jasperPath = Storage::path('reports/format_t/FORMAT_T.jasper');
            $outputPath = Storage::path('reports/format_t/FORMAT_T.pdf');
            $imageAsset = Storage::path('assets/LogoFormaT.png');
        }

        unset($procedure->shape['template_shape']);
        unset($procedure->shape['procedure']);
        unset($procedure->shape['grantors']);

        $jsonData = json_encode($procedure);

        Storage::put("reports/tempJson.json", $jsonData);

        $parameters += ['imageSF' => $imageAsset];

        $pdf = new Report(
            Storage::path('reports/tempJson.json'),
            $parameters,
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
            $result[$prefix . $i] = $inputSplit[$i] ?? '';
        }

        return $result;
    }

    /**
     * @param $procedure
     * @param $templateShapeType
     * @return mixed
     */
    private function prepareData($procedure, $templateShapeType)
    {
        if ($templateShapeType == TemplateShape::FORM01) {
            //ALIENATING DATA
            $procedure->shape->alienatingData = $this->grantorData($procedure, 0);

            //ACQUIRER DATA
            $procedure->shape->acquirerData = $this->grantorData($procedure, 1);

            $procedure->shape->alien_rfc_s = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[0]['rfc'] : $procedure->shape->data_form['alienating_rfc'],
                13,
                'al_rfc'
            );
            $procedure->shape->alien_curp_s = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[0]['curp'] : $procedure->shape->data_form['alienating_crup'],
                18,
                'al_curp'
            );
            $procedure->shape->acq_rfc_s = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[1]['rfc'] : $procedure->shape->data_form['acquirer_rfc'],
                13,
                'ac_rfc'
            );
            $procedure->shape->acq_curp_s = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[1]['curp'] : $procedure->shape->data_form['acquirer_curp'],
                18,
                'ac_curp'
            );
        } else {
            //ACQUIRER DATA
            $procedure->shape->acquirerData = [
                "name" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[1]['name'] : $procedure->shape->data_form['acquirer_name'],
            ];

            $procedure->shape->rfc = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[1]['rfc'] : $procedure->shape->data_form['rfc'],
                13,
                'rfc'
            );
            $procedure->shape->curp = $this->splitString(
                ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[1]['curp'] : $procedure->shape->data_form['curp'],
                18,
                'curp'
            );
        }

        if ($templateShapeType == TemplateShape::FORM03) {
            //ALIENATING DATA
            $procedure->shape->alienatingData = $this->grantorData($procedure, 0);
        }

        if ($procedure->shape->grantors->isNotEmpty()) {
            if (count($procedure->shape->grantors) > Shape::REQUIRED_GRANTORS) {
                $grantors = $procedure->shape->grantors->splice(2);

                $acquirers = [];
                $alienating = [];

                foreach ($grantors as $grantor) {
                    if ($grantor->pivot->type == Stake::ACQUIRER) {
                        $acquirers[] = $grantor;
                    }

                    if ($grantor->pivot->type == Stake::ALIENATING) {
                        $alienating[] = $grantor;
                    }
                }

                $procedure->shape->extra = [
                    'acquirers' => $acquirers,
                    'alientating' => $alienating,
                ];
            }
        }

        return $procedure;
    }

    private function grantorData($procedure, $index)
    {
        return [
            "name" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['name'] : $procedure->shape->data_form['alienating_name'],
            "street" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['name'] : $procedure->shape->data_form['alienating_street'],
            "outdoor_number" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['no_ext'] : $procedure->shape->data_form['alienating_outdoor_number'],
            "interior_number" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['no_int'] : $procedure->shape->data_form['alienating_interior_number'],
            "colony" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['colony'] : $procedure->shape->data_form['alienating_colony'],
            "locality" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['locality'] : $procedure->shape->data_form['alienating_locality'],
            "municipality" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['municipality'] : $procedure->shape->data_form['alienating_municipality'],
            "entity" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['municipality'] : $procedure->shape->data_form['alienating_entity'],
            "zipcode" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['zipcode'] : $procedure->shape->data_form['alienating_zipcode'],
            "phone" => ($procedure->shape->grantors->isNotEmpty()) ? $procedure->shape->grantors[$index]['phone'] : $procedure->shape->data_form['alienating_phone'],
        ];
    }
}
