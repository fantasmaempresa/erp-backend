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

        $procedure->shape = $shape;
        $procedure->shape->operation = $shape->operation;
        $procedure->shape->signature_date_s = $this->separateDate(new DateTime($procedure->shape->signature_date));

        $parameters = [];

        $extension = "pdf";
        if ($request->has('reportExtension')) {
            switch ($request->get('reportExtension')) {
                case 1:
                    $extension = "pdf";
                    break;
                case 2:
                    $extension = "rtf";
                    break;
                case 3:
                    $extension = "xls";
                    break;
            }
        }

        if ($shape->template_shape->id == TemplateShape::FORM01) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM01);

            $parameters += ['subReportPath' => Storage::path('reports/format_1/')];

            $jasperPath = Storage::path('reports/format_1/FORMAT1.jasper');
            $outputPath = Storage::path('reports/format_1/FORMAT1.' . $extension);
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM02) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM02);

            $parameters += ['subReportPath' => Storage::path('reports/format_2/')];

            $jasperPath = Storage::path('reports/format_2/FORMAT2.jasper');
            $outputPath = Storage::path('reports/format_2/FORMAT2.' . $extension);
            $imageAsset = Storage::path('assets/LogoFinanzas.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM03) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM03);

            $parameters += ['subReportPath' => Storage::path('reports/format_c/')];
            $jasperPath = Storage::path('reports/format_c/FORMAT_C.jasper');
            $outputPath = Storage::path('reports/format_c/FORMAT_C.' . $extension);
            $imageAsset = Storage::path('assets/LogoFormaC.png');
        } elseif ($shape->template_shape->id == TemplateShape::FORM04) {
            $procedure = $this->prepareData($procedure, TemplateShape::FORM04);

            $parameters += ['subReportPath' => Storage::path('reports/format_t/')];
            $jasperPath = Storage::path('reports/format_t/FORMAT_T.jasper');
            $outputPath = Storage::path('reports/format_t/FORMAT_T.' . $extension);
            $imageAsset = Storage::path('assets/LogoFormaT.png');
        }

        unset($procedure->shape['template_shape']);
        unset($procedure->shape['procedure']);
        unset($procedure->shape['grantors']);

        // return $this->showList($procedure);

        $jsonData = json_encode($procedure);

        Storage::put("reports/tempJson.json", $jsonData);

        $parameters += ['imageSF' => $imageAsset];

        $pdf = new Report(
            Storage::path('reports/tempJson.json'),
            $parameters,
            $jasperPath,
            $outputPath,
            $extension
        );

        $result = $pdf->generateReport();
        Storage::delete("reports/tempJson.json");

        return ($result['success'] || $extension == "rtf") ? $this->downloadFile($outputPath) : $this->errorResponse($result['message'], 500);
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
            $grantor = $procedure->shape->grantors[1] ?? null;

            //ACQUIRER DATA
            $procedure->shape->acquirerData = [
                'name' => $grantor ? $grantor['name'] : $procedure->shape->data_form['acquirer_name'],
                'father_last_name' => $grantor ? $grantor['father_last_name'] : '',
                'mother_last_name' => $grantor ? $grantor['mother_last_name'] : '',
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
        $grantor = $procedure->shape->grantors[$index] ?? null;

        return [
            'name' => $grantor ? $grantor['name'] : $procedure->shape->data_form['alienating_name'],
            'father_last_name' => $grantor ? $grantor['father_last_name'] : '',
            'mother_last_name' => $grantor ? $grantor['mother_last_name'] : '',
            'street' => $grantor ? $grantor['street'] : $procedure->shape->data_form['alienating_street'],
            'outdoor_number' => $grantor ? $grantor['no_ext'] : $procedure->shape->data_form['alienating_outdoor_number'],
            'interior_number' => $grantor ? $grantor['no_int'] : $procedure->shape->data_form['alienating_interior_number'],
            'colony' => $grantor ? $grantor['colony'] : $procedure->shape->data_form['alienating_colony'],
            'locality' => $grantor ? $grantor['locality'] : $procedure->shape->data_form['alienating_locality'],
            'municipality' => $grantor ? $grantor['municipality'] : $procedure->shape->data_form['alienating_municipality'],
            'entity' => $grantor ? $grantor['entity'] : $procedure->shape->data_form['alienating_entity'],
            'zipcode' => $grantor ? $grantor['zipcode'] : $procedure->shape->data_form['alienating_zipcode'],
            'phone' => $grantor ? $grantor['phone'] : $procedure->shape->data_form['alienating_phone'],
        ];
    }
}
