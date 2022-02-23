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
     */
    public function calculateReactiveProjectQuote(Request $request): JsonResponse
    {
        if (!($request->has('operation_fields') && $request->has('op_total'))) {
            return $this->errorResponse("error: format request, field not found", 404);
        }

        $opTotal = $request->get('operation_total');


        return $this->showList([]);
    }

    /**
     * @param array $opField
     * @param array $opTotal
     * @param array $formQuote
     */
    public function calculateProjectQuote(array $opField, array $opTotal, array $formQuote)
    {
        $result = [];
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
                $fieldResult = [
                    'key' => $field['field'],
                    'concept_id' => $concept->id,
                    'value' => Concept::getOperation($concept->formula, $concept->amount, $field['value']),
                ];
            }
            $result['operation_fields'][] = $fieldResult;
        }
    }
}
