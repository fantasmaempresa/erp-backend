<?php

/*
 * CODE
 * ProjectQuote Operations Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use App\Http\Controllers\ApiController;
use App\Models\Concept;
use App\Models\ProjectQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuoteOperationsController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function calculateReactiveProjectQuote(Request $request): JsonResponse
    {
        $this->validate($request, [
            'quote' => 'required',
        ]);

        if (!(
            isset($request->all()['quote']['operations']['operation_fields']) &&
            isset($request->all()['quote']['operations']['operation_total']) &&
            isset($request->all()['quote']['form'])
        )) {
            return $this->errorResponse('fields not found', 404);
        }

        return $this->showList($this->calculateProjectQuote(
            $request->all()['quote']['operations']['operation_fields'],
            $request->all()['quote']['operations']['operation_total'],
            $request->all()['quote']['form']
        ));
    }

    /**
     * @param array $opField
     * @param array $opTotal
     * @param array $formQuote
     *
     * @return array
     */
    public function calculateProjectQuote(array $opField, array $opTotal, array $formQuote): array
    {
        $result = [];
        $total = 0;
        foreach ($opField as $field) {
            $value = 0;
            $fieldResult = [];
            foreach ($formQuote as $item) {
                if ($item['key'] === $field['key']) {
                    $value = $item['value'];
                    break;
                }
            }

            if ($value) {
                $concept = Concept::findOrFail($field['concept']['id']);
                if (!isset($result['operation_fields'][$field['key']]['total'])) {
                    $result['operation_fields'][$field['key']]['total'] = $value;
                }

                $result['operation_fields'][$field['key']]['description'][] = [
                    'concept_id' => $concept->id,
                    'value_concept' => $this->getValueConcept($concept->formula, $concept->amount, $value),
                    'value' => $value,
                    'operation' => $concept->formula['operation'],
                ];

                $result['operation_fields'][$field['key']]['total'] = $this->executeOperationConcept(
                    $concept->formula['operation'],
                    $this->getValueConcept(
                        $concept->formula,
                        $concept->amount,
                        $value
                    ),
                    $result['operation_fields'][$field['key']]['total']
                );
            }
        }

        foreach ($result['operation_fields'] as $key => $item) {
            $total = $item['total'];
        }

        foreach ($opTotal as $field) {
            if (!isset($result['operation_total']['total'])) {
                $result['operation_total']['total'] = $total;
            }

            $concept = Concept::findOrFail($field['concept']['id']);
            $result['operation_total']['description'][] = [
                'concept_id' => $concept->id,
                'value_concept' => $this->getValueConcept($concept->formula, $concept->amount, $total),
                'value' => $total,
                'operation' => $concept->formula['operation'],
            ];

            $result['operation_total']['total'] = $this->executeOperationConcept(
                $concept->formula['operation'],
                $this->getValueConcept(
                    $concept->formula,
                    $concept->amount,
                    $total
                ),
                $result['operation_total']['total']
            );
        }

        return $result;
    }

    /**
     * @param array $formula
     * @param float $amountConcept
     * @param float $valueField
     *
     * @return float|int
     */
    public function getValueConcept(array $formula, float $amountConcept, float $valueField): float|int
    {
        if (isset($formula['percentage']) && $formula['percentage']) {
            $amountConcept = ($amountConcept * $valueField) / 100;
        }

        return $amountConcept;
    }

    /**
     * @param string $operation
     * @param float  $amountConcept
     * @param float  $value
     *
     * @return float|int
     */
    public function executeOperationConcept(string $operation, float $amountConcept, float $value): float|int
    {
        return match ($operation) {
            '+' => $value + $amountConcept,
            '-' => $value - $amountConcept,
            '/' => $value / $amountConcept,
            '*' => $value * $amountConcept,
            default => 0
        };
    }
}
