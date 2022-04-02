<?php

/*
 * CODE
 * ProjectQuote Operations Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use App\Http\Controllers\ApiController;
use App\Models\Concept;
use App\Models\ProjectQuote;
use Dflydev\DotAccessData\Data;
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
            isset($request->all()['quote']['operations']['operation_total'])
        )) {
            return $this->errorResponse('fields not found', 404);
        }

        return $this->showList($this->calculateProjectQuote(
            $request->all()['quote']['operations']['operation_fields'],
            $request->all()['quote']['operations']['operation_total']
        ));
    }

    /**
     * @param array $opField
     * @param array $opTotal
     *
     * @return array
     */
    public function calculateProjectQuote(array $opField, array $opTotal): array
    {
        $result = [];
        $total = 0;
        $discount = [];
        foreach ($opField as $field) {
            if ($field['value']) {
                foreach ($field['concepts'] as $concept) {
                    $conceptB = Concept::findOrFail($concept['id']);
                    if ($conceptB->formula['operation'] === '-') {
                        $discount[$field['key']][] = $conceptB;
                        continue;
                    }

                    if (!isset($result['operation_fields'][$field['key']]['total'])) {
                        $result['operation_fields'][$field['key']]['total'] = $field['value'];
                    }

                    $result['operation_fields'][$field['key']]['description'][] = [
                        'concept_id' => $conceptB->id,
                        'value_concept' => $this->getValueConcept($conceptB->formula, $conceptB->amount, $field['value']),
                        'value' => $field['value'],
                        'concept' => $conceptB,
                        'operation' => $conceptB->formula['operation'],
                        'subtotal' => $this->executeOperationConcept(
                            $conceptB->formula['operation'],
                            $this->getValueConcept(
                                $conceptB->formula,
                                $conceptB->amount,
                                $field['value']
                            ),
                            $field['value']
                        ),
                    ];

                    $result['operation_fields'][$field['key']]['total'] += $this->executeOperationConcept(
                        $conceptB->formula['operation'],
                        $this->getValueConcept(
                            $conceptB->formula,
                            $conceptB->amount,
                            $field['value']
                        ),
                        $field['value']
                    );
                }
            }
        }

        foreach ($result['operation_fields'] as $key => $item) {
            $total += $item['total'];
        }

        if (empty($opTotal)) {
            $result['operation_total']['total'] = $total;
        } else {
            foreach ($opTotal as $field) {
                if (!isset($result['operation_total']['total'])) {
                    $result['operation_total']['total'] = $total;
                }

                foreach ($field['concepts'] as $concept) {
                    $conceptB = Concept::findOrFail($concept['id']);
                    $result['operation_total']['description'][] = [
                        'concept_id' => $conceptB->id,
                        'value_concept' => $this->getValueConcept($conceptB->formula, $conceptB->amount, $total),
                        'concept' => $conceptB,
                        'value' => $total,
                        'subtotal' => $this->executeOperationConcept(
                            $conceptB->formula['operation'],
                            $this->getValueConcept(
                                $conceptB->formula,
                                $conceptB->amount,
                                $total
                            ),
                            $total
                        ),
                        'operation' => $conceptB->formula['operation'],
                    ];
                    $result['operation_total']['total'] += $this->executeOperationConcept(
                        $conceptB->formula['operation'],
                        $this->getValueConcept(
                            $conceptB->formula,
                            $conceptB->amount,
                            $total
                        ),
                        $total
                    );
                }
            }
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
            '/' => $value ? 0 : ($value / $amountConcept),
            '*' => $value * $amountConcept,
            default => 0
        };
    }
}
