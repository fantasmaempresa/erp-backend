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
        $procedure->staff;
        $procedure->staff->abbreviation = $procedure->staff->initials();

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
            //AMOUNT DATA
            $auxDataForm = $procedure->shape->data_form;

            $auxDataForm['value_catastral'] = $this->formatCurrency($procedure->shape->data_form['value_catastral']);
            $procedure->shape->operation_value = $this->formatCurrency($procedure->shape->operation_value);

            $procedure->shape->data_form = $auxDataForm;
            //ALIENATING DATA
            $grantorAlienating = $procedure->shape->grantors()->where('principal', true)->where('grantor_shape.type', Stake::ALIENATING)->first();
            $procedure->shape->alienatingData = $this->grantorAlienating($grantorAlienating, $procedure);

            //ACQUIRER DATA
            $grantorAcquirer = $procedure->shape->grantors()->where('principal', true)->where('grantor_shape.type', Stake::ACQUIRER)->first();
            $procedure->shape->acquirerData = $this->grantorAcquirer($grantorAcquirer, $procedure);

            //ALIENATING RFC AND CURP
            $grantorRfc = $grantorAlienating?->rfc ?? $procedure->shape->data_form['alienating_rfc'] ?? null;
            $procedure->shape->alien_rfc_s = $this->splitString($grantorRfc, 13, 'al_rfc');

            $grantorCurp = $grantorAlienating?->curp ?? $procedure->shape->data_form['alienating_crup'] ?? null;
            $procedure->shape->alien_curp_s = $this->splitString($grantorCurp, 18, 'al_curp');

            //ACQUIRER RFC AND CURP
            $grantorRfc = $grantorAcquirer?->rfc ?? $procedure->shape->data_form['acquirer_rfc'] ?? null;
            $procedure->shape->acq_rfc_s = $this->splitString($grantorRfc, 13, 'ac_rfc');

            $grantorCurp = $grantorAcquirer?->curp ?? $procedure->shape->data_form['acquirer_curp'] ?? null;
            $procedure->shape->acq_curp_s = $this->splitString($grantorCurp, 18, 'ac_curp');
        } else {
            //AMOUNT DATA
            $procedure->shape->operation_value = $this->formatCurrency($procedure->shape->operation_value);
          
            //ALIENATING DATA
            $grantorAlienating = $procedure->shape->grantors()->where('principal', true)->where('grantor_shape.type', Stake::ALIENATING)->first();
            $procedure->shape->alienatingData = $this->grantorAlienating($grantorAlienating, $procedure);

            $grantorRfc = $grantorAlienating?->rfc ?? $procedure->shape->data_form['rfc'] ?? null;
            $procedure->shape->rfc = $this->splitString($grantorRfc, 13, 'rfc');

            $grantorCurp = $grantorAlienating?->curp ?? $procedure->shape->data_form['curp'] ?? null;
            $procedure->shape->curp = $this->splitString($grantorCurp, 18, 'curp');
        }

        $grantorsAcquirers = $procedure->shape->grantors()->where('principal', false)->where('grantor_shape.type', Stake::ACQUIRER)->get();
        $grantorsAlienating = $procedure->shape->grantors()->where('principal', false)->where('grantor_shape.type', Stake::ALIENATING)->get();

        $acquirers = null;
        $alienating = null;

        foreach ($grantorsAcquirers as $grantor) {
            $acquirers[] = $grantor;
        }

        foreach ($grantorsAlienating as $index => $grantor) {
            if ($templateShapeType == TemplateShape::FORM02 && $index == 0) {
                $alienatingData = $procedure->shape->alienatingData;

                $alienatingData['complete_name'] = $alienatingData['complete_name']
                    . " y " . $grantor->father_last_name . " " . $grantor->mother_last_name . " " . $grantor->name;

                $procedure->shape->alienatingData = $alienatingData;
            } else {
                $alienating[] = $grantor;
            }
        }

        $procedure->shape->extra = [
            'acquirers' => $acquirers,
            'alientating' => $alienating,
        ];

        return $procedure;
    }

    private function grantorAlienating($grantor, $procedure)
    {
        $result = [];
        if (isset($grantor)) {
            $result['complete_name'] = strtoupper(($grantor['father_last_name'] ?? '') . " " . ($grantor['mother_last_name'] ?? '') . " " . ($grantor['name'] ?? ''));
            $result['street'] = strtoupper($grantor['street']) ?? '';
            $result['outdoor_number'] = strtoupper($grantor['no_ext']) ?? '';
            $result['interior_number'] = strtoupper($grantor['no_int']) ?? '';
            $result['colony'] = strtoupper($grantor['colony']) ?? '';
            $result['locality'] = strtoupper($grantor['locality']) ?? '';
            $result['municipality'] = strtoupper($grantor['municipality']) ?? '';
            $result['entity'] = strtoupper($grantor['entity']) ?? '';
            $result['zipcode'] = strtoupper($grantor['zipcode']) ?? '';
            $result['phone'] = strtoupper($grantor['phone']) ?? '';
        } else {
            $result['complete_name'] = strtoupper($procedure->shape->data_form['alienating_name']) ?? '';
            $result['street'] = strtoupper($procedure->shape->data_form['alienating_street']) ?? '';
            $result['outdoor_number'] = strtoupper($procedure->shape->data_form['alienating_outdoor_number']) ?? '';
            $result['interior_number'] = strtoupper($procedure->shape->data_form['alienating_interior_number']) ?? '';
            $result['colony'] = strtoupper($procedure->shape->data_form['alienating_colony']) ?? '';
            $result['locality'] = strtoupper($procedure->shape->data_form['alienating_locality']) ?? '';
            $result['municipality'] = strtoupper($procedure->shape->data_form['alienating_municipality']) ?? '';
            $result['entity'] = strtoupper($procedure->shape->data_form['alienating_entity']) ?? '';
            $result['zipcode'] = strtoupper($procedure->shape->data_form['alienating_zipcode']) ?? '';
            $result['phone'] = strtoupper($procedure->shape->data_form['alienating_phone']) ?? '';
        }
        return $result;
    }

    private function grantorAcquirer($grantor, $procedure)
    {
        $result = [];

        if (isset($grantor)) {
            $result['complete_name'] = strtoupper(($grantor['father_last_name'] ?? '') . " " . ($grantor['mother_last_name'] ?? '') . " " . ($grantor['name'] ?? ''));
            $result['street'] = strtoupper($grantor['street']) ?? '';
            $result['outdoor_number'] = strtoupper($grantor['no_ext']) ?? '';
            $result['interior_number'] = strtoupper($grantor['no_int']) ?? '';
            $result['colony'] = strtoupper($grantor['colony']) ?? '';
            $result['locality'] = strtoupper($grantor['locality']) ?? '';
            $result['municipality'] = strtoupper($grantor['municipality']) ?? '';
            $result['entity'] = strtoupper($grantor['entity']) ?? '';
            $result['zipcode'] = strtoupper($grantor['zipcode']) ?? '';
            $result['phone'] = strtoupper($grantor['phone']) ?? '';
        } else {
            $result['complete_name'] = strtoupper($procedure->shape->data_form['alienating_name']) ?? '';
            $result['street'] = strtoupper($procedure->shape->data_form['acquirer_street']) ?? '';
            $result['outdoor_number'] = strtoupper($procedure->shape->data_form['acquirer_outdoor_number']) ?? '';
            $result['interior_number'] = strtoupper($procedure->shape->data_form['acquirer_interior_number']) ?? '';
            $result['colony'] = strtoupper($procedure->shape->data_form['acquirer_colony']) ?? '';
            $result['locality'] = strtoupper($procedure->shape->data_form['acquirer_locality']) ?? '';
            $result['municipality'] = strtoupper($procedure->shape->data_form['acquirer_municipality']) ?? '';
            $result['entity'] = strtoupper($procedure->shape->data_form['acquirer_entity']) ?? '';
            $result['zipcode'] = strtoupper($procedure->shape->data_form['acquirer_zipcode']) ?? '';
            $result['phone'] = strtoupper($procedure->shape->data_form['acquirer_phone']) ?? '';
        }

        return $result;
    }

    private function formatCurrency($number)
    {
        if (is_numeric($number)) {
            $number = (float) $number;
            return '$' . number_format($number, 2, '.', ',');
        } else {
            return $number;
        }
    }
}
