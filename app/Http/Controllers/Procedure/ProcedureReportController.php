<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Book;
use App\Models\Procedure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Open2code\Pdf\jasper\Report;

class ProcedureReportController extends ApiController
{
    public function folioContol(Request $request)
    {
        $unusedFolios = $request->get('unused_folios') ?? false;

        if (empty($request->get('book_id'))) {
            $book = new Book();
        } else {
            $book = Book::where('book_id', $request->get('book_id'));
        }

        $reportFolioControl = [];

        $book->join('folios', 'folios.book_id', '=', 'books.id')
            ->select('books.id as book_id', 'books.name as book', 'books.folio_min as book_folio_min', 'books.folio_max as book_folio_max', 'folios.*')
            ->orderBy('books.name', 'asc')
            ->orderBy('folios.name', 'asc')
            ->chunk(1000, function ($bookFolios) use (&$reportFolioControl, $unusedFolios) {
                if ($unusedFolios) {
                    $this->fillUnusedFolio($bookFolios, $reportFolioControl);
                } else {
                    foreach ($bookFolios as $folio) {
                        $this->prepareFolioData($folio, $reportFolioControl);
                    }
                }
            });

        // return $this->showList($reportFolioControl);

        $jsonData = json_encode($reportFolioControl);
        Storage::put("reports/tempJson.json", $jsonData);

        $outputPath = Storage::path('reports/folio_control/FOLIO_CONTROL.xls');

        $pdf = new Report(
            Storage::path('reports/tempJson.json'),
            [],
            Storage::path('reports/folio_control/FOLIO_CONTROL.jasper'),
            $outputPath,
            'xls'
        );

        $result = $pdf->generateReport();
        Storage::delete("reports/tempJson.json");

        return ($result['success']) ? $this->downloadFile($outputPath) : $this->errorResponse($result['message'], 500);
    }

    private function modifyDate($date)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);

        if ($carbonDate->year < 100) {
            $correctYear = $carbonDate->year + 2000;
            $carbonDate->year($correctYear);
        }

        return $carbonDate->format('Y-m-d');
    }

    private function fillUnusedFolio($bookFolios, &$reportFolioControl)
    {
        $folioCount = $bookFolios->count();

        $previousMaxFolioNumber = 0;
        foreach ($bookFolios as $key => $folio) {
            if ($key === 0) {
                if ($folio->folio_min > $folio->book_folio_min) {
                    $reportFolioControl[] = $this->unusedFolio($folio, $folio->book_folio_min . ' - ' . ($folio->folio_min - 1));
                }

                $this->prepareFolioData($folio, $reportFolioControl);
                $previousMaxFolioNumber = $folio->folio_max;
            } else if ($key === ($folioCount - 1)) {
                if ($folio->folio_min > ($previousMaxFolioNumber + 1)) {
                    $reportFolioControl[] = $this->unusedFolio($folio, ($previousMaxFolioNumber + 1) . ' - ' . ($folio->folio_min - 1));
                }

                $this->prepareFolioData($folio, $reportFolioControl);

                if ($folio->folio_max < $folio->book_folio_max) {
                    $reportFolioControl[] = $this->unusedFolio($folio, ($folio->folio_max + 1) . ' - ' . $folio->book_folio_max);
                }
            } else {
                if ($folio->folio_min > ($previousMaxFolioNumber + 1)) {
                    $reportFolioControl[] = $this->unusedFolio($folio, ($previousMaxFolioNumber + 1) . ' - ' . ($folio->folio_min - 1));
                }

                $this->prepareFolioData($folio, $reportFolioControl);
                $previousMaxFolioNumber = $folio->folio_max;
            }
        }
    }

    private function prepareFolioData($folio, &$reportFolioControl)
    {
        $procedure = Procedure::find($folio->procedure_id);

        $date = '';
        $operation = '';
        $canceledFolios = $this->getCanceledFolios($folio);
        $canceledFoliosUsers = $this->getCanceledFoliosUsers($folio);
        $registration = 'NO';

        if (!is_null($procedure)) {
            $date = $this->modifyDate($procedure->date);
            $registration = $procedure->registrationProcedureData ? 'SI' : 'NO';
            if (!is_null($procedure->operations)) {
                $operation = $procedure->operations[0]->name;
            }

            foreach ($procedure->grantors as $key => $grantor) {
                $grantorName = $grantor->name . ' ' .
                    ($grantor->father_last_name === 'BK' || is_null($grantor->father_last_name) ? '' : $grantor->father_last_name) . ' ' .
                    ($grantor->mother_last_name === 'BK' || is_null($grantor->mother_last_name) ? '' : $grantor->mother_last_name);

                if ($key == 0) {
                    $reportFolioControl[] = [
                        'instrument' => $folio->name,
                        'date' => $date,
                        'book' => $folio->book,
                        'folios' => $folio->folio_min . ' - ' . $folio->folio_max,
                        'canceled_folios' => $canceledFolios,
                        'canceled_folios_users' => $canceledFoliosUsers,
                        'record' => $procedure->name,
                        'registration' => $registration,
                        'grantor' => $grantorName,
                        'operations' => $operation,
                    ];
                } else {
                    $reportFolioControl[] = [
                        'instrument' => '',
                        'date' => '',
                        'book' => $folio->book,
                        'folios' => '',
                        'canceled_folios' => '',
                        'canceled_folios_users' => '',
                        'registration' => '',
                        'record' => '',
                        'grantor' => $grantorName,
                        'operations' => ''
                    ];
                }
            }
        } else {
            $reportFolioControl[] = [
                'instrument' => $folio->name,
                'date' => $date,
                'book' => $folio->book,
                'folios' => $folio->folio_min . ' - ' . $folio->folio_max,
                'canceled_folios' => $canceledFolios,
                'canceled_folios_users' => $canceledFoliosUsers,
                'registration' => $registration,
                'record' => '',
                'grantor' => '',
                'operations' => $operation,
            ];
        }
    }

    private function unusedFolio($folio, $foliosRange)
    {
        return [
            'instrument' => 'FOLIOS SIN USAR',
            'date' => '',
            'book' => $folio->book,
            'folios' => $foliosRange,
            'canceled_folios' => '',
            'canceled_folios_users' => '',
            'registration' => '',
            'record' => '',
            'grantor' => '',
            'operations' => ''
        ];
    }

    private function getCanceledFolios($folio)
    {
        $canceledFolios = [];
        if (!is_null($folio->unused_folios)) {
            $unusedFolios = json_decode($folio->unused_folios);
            foreach ($unusedFolios as $unusedFolio) {
                $canceledFolios[] = $unusedFolio->folio;
            }
        }

        return implode(', ', $canceledFolios);
    }

    private function getCanceledFoliosUsers($folio)
    {
        $users = [];

        if (!is_null($folio->unused_folios)) {
            $unusedFolios = json_decode($folio->unused_folios);
            foreach ($unusedFolios as $unusedFolio) {
                $users[] = User::find($unusedFolio->user_id)->name;
            }
        }

        return implode(', ', $users);
    }
}
