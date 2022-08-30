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
                if (!empty($field['concepts'])) {
                    foreach ($field['concepts'] as $concept) {
                        $conceptB = Concept::findOrFail($concept['id']);

                        if (!isset($result['operation_fields'][$field['key']]['total'])) {
                            $result['operation_fields'][$field['key']]['total'] = 0;
                        }
                        $result['operation_fields'][$field['key']]['original_value'] = $field['value'];
                        $result['operation_fields'][$field['key']]['description'][] = [
                            'concept_id' => $conceptB->id,
                            'value_concept' => $this->getValueConcept($conceptB->formula, $conceptB->amount, $field['value']),
                            'value' => $field['value'],
                            'concept' => $conceptB,
                            'operation' => $conceptB->formula['operation'],
                            'subtotal' => $this->executeOperationConcept(
                                $conceptB->formula,
                                $this->getValueConcept(
                                    $conceptB->formula,
                                    $conceptB->amount,
                                    $field['value']
                                ),
                                $field['value']
                            ),
                        ];

                        //aplicar operación
                        $result['operation_fields'][$field['key']]['total'] += $this->executeOperationConcept(
                            $conceptB->formula,
                            $this->getValueConcept(
                                $conceptB->formula,
                                $conceptB->amount,
                                $field['value']
                            ),
                            $field['value']
                        );
                    }

                } else {
                    $result['operation_fields'][$field['key']]['total'] = $field['value'];
                    $result['operation_fields'][$field['key']]['original_value'] = $field['value'];
                }
            } else {
                $result['operation_fields'][$field['key']]['total'] = 0;
                $result['operation_fields'][$field['key']]['original_value'] = 0;

            }
        }

        if (isset($result['operation_fields'])) {
            foreach ($result['operation_fields'] as $item) {
                $total += $item['total'];
            }

            if (empty($opTotal)) {
                $result['operation_total']['subtotal'] = $total;
                $result['operation_total']['total'] = $total;
                $result['operation_total']['description'] = null;
            } else {
                $result['operation_total']['subtotal'] = $total;
                foreach ($opTotal as $field) {
                    $result['operation_total']['subtotal'] = $total;
                    if (!isset($result['operation_total']['total'])) {
                        $result['operation_total']['total'] = $total;
                    }
                    if (empty($field['concepts'])) {
                        $result['operation_total']['description'][] = null;
                    } else {
                        foreach ($field['concepts'] as $concept) {
                            $result['operation_total']['description'][] = [
                                'concept_id' => $conceptB->id,
                                'value_concept' => $this->getValueConcept($conceptB->formula, $conceptB->amount, $total),
                                'concept' => $conceptB,
                                'value' => $total,
                                'subtotal' => $this->executeOperationConcept(
                                    $conceptB->formula,
                                    $this->getValueConcept(
                                        $conceptB->formula,
                                        $conceptB->amount,
                                        $total
                                    ),
                                    $total,
                                    true
                                ),
                                'operation' => $conceptB->formula['operation'],
                            ];
                            $result['operation_total']['total'] += $this->executeOperationConcept(
                                $conceptB->formula,
                                $this->getValueConcept(
                                    $conceptB->formula,
                                    $conceptB->amount,
                                    $total
                                ),
                                $total,
                                true
                            );
                        }
                    }
                }
            }
        }





        
        return $result;
    }

    /**
     * @param array $formula
     * @param float $amount
     * @param float $value
     *
     * @return mixed
     */
    public function getValueConcept(array $formula, float $amount, float $value): mixed
    {
        return match (true) {
            $formula['validity']['apply'] => $this->calculateValidity($formula['validity'], $amount, $value),
            $formula['range']['apply'] => $this->calculateRange($formula['range']['between'], $value),
            $formula['percentage'] => $this->calculatePercentage($amount, $value),
            default => $amount
        };
    }

    /**
     * @param array $formula
     * @param float $amount
     * @param float $value
     *
     * @return mixed
     */
    public function calculateValidity(array $formula, float $amount, float $value): mixed
    {
        $valueReturn = 0;
        if ($formula['is_date']) {
            $date = date('d-m-Y', strtotime(date("d-m-Y") . ' - ' . $formula['validity_year'] . ' year'));
            $date = explode("-", $date);
            $date = $date[2] * 1;
        } else {
            $date = $formula['validity_year'];
        }

        if ($formula['is_range']) {
            $valueReturn = $this->calculateRange($formula['between'], $date);
        } else {
            if ($value < $date) {
                $valueReturn = $amount;
            }
        }

        return $valueReturn;
    }

    /**
     * @param array $between
     * @param mixed $value
     *
     * @return mixed
     */
    public function calculateRange(array $between, mixed $value): mixed
    {
        foreach ($between as $item) {
            if ($value >= $item['min'] && $value <= $item['max']) {
                return $item['amount'];
            }
        }

        return 0;
    }

    /**
     * @param float $amount
     * @param float $value
     *
     * @return float
     */
    public function calculatePercentage(float $amount, float $value): float
    {
        return ($amount * $value) / 100;
    }

    /**
     * @param array $formula
     * @param float $valueConcept
     * @param float $value
     * @param ?bool $total
     *
     * @return float|int
     */
    public function executeOperationConcept(array $formula, float $valueConcept, float $value, ?bool $total = false): float|int
    {
        return match (true) {
            $formula['validity']['apply'] => $valueConcept,
            $formula['operable'] && ($formula['operation'] === '+') => $value + $valueConcept,
            $formula['operable'] && ($formula['operation'] === '-') => $total ? $valueConcept * -1 : $value - $valueConcept,
            $formula['operable'] && ($formula['operation'] === '+/') => $value + ($value ? 0 : ($value / $valueConcept)),
            $formula['operable'] && ($formula['operation'] === '+*') => $value + ($value * $valueConcept),
            $formula['operable'] && ($formula['operation'] === '-/') => $value - ($value ? 0 : ($value / $valueConcept)),
            $formula['operable'] && ($formula['operation'] === '-*') => $value - ($value * $valueConcept),
            !$formula['operable'] && ($formula['operation'] === '/') => $value ? 0 : ($value / $valueConcept),
            !$formula['operable'] && ($formula['operation'] === '*') => $value * $valueConcept,
            default => $valueConcept
        };
    }
}
