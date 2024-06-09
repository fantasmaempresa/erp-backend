<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Grantor;
use App\Models\Procedure;
use App\Models\RegistrationProcedureData;
use App\Models\Shape;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcedureBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $import = new ImportCSV(Storage::path('backup_server/procedures_backup.csv'), delimeter: '|');
        $procedures = $import->readFile();
        $procedures = $procedures->where('user_id', '!=', 6)->values();

        $import = new ImportCSV(Storage::path('backup_server/grantors_backup.csv'), delimeter: '|');
        $grantorsBackup = $import->readFile();

        $import = new ImportCSV(Storage::path('backup_server/grantor_procedure_backup.csv'), delimeter: '|');
        $grantorProcedure = $import->readFile();

        $import = new ImportCSV(Storage::path('backup_server/registration_procedure_data_backup.csv'), delimeter: '|');
        $registrationProcedureData = $import->readFile();

        $import = new ImportCSV(Storage::path('backup_server/shapes_backup.csv'), delimeter: '|');
        $shapes = $import->readFile();

        $import = new ImportCSV(Storage::path('backup_server/grantor_shape_backup.csv'), delimeter: '|');
        $grantorShape = $import->readFile();

        DB::beginTransaction();
        try {
            foreach ($procedures as $procedure) {
                $procedureToInsert = $procedure;
                unset($procedureToInsert['operation_id']);
                unset($procedureToInsert['id']);
                $procedureToInsert['date_proceedings'] = (!empty($procedureToInsert['date_proceedings'])) ? $procedureToInsert['date_proceedings'] : null;
                $procedureToInsert['value_operation'] = (!empty($procedureToInsert['value_operation'])) ? $procedureToInsert['value_operation'] : null;
                $procedureToInsert['folio_min'] = (!empty($procedureToInsert['folio_min'])) ? $procedureToInsert['folio_min'] : null;
                $procedureToInsert['credit'] = (!empty($procedureToInsert['credit'])) ? $procedureToInsert['credit'] : null;
                $procedureToInsert['observation'] = (!empty($procedureToInsert['observation'])) ? $procedureToInsert['observation'] : null;
                $procedureToInsert['user_id'] = 6;

                $procedureToInsert = Procedure::create($procedureToInsert);
                $procedureToInsert->operations()->attach($procedure['operation_id']);

                //GRANTOR_PROCEDURE
                $grantorProcedure = $grantorProcedure->where('procedure_id', $procedure['id'])->values();
                foreach ($grantorProcedure as $grantor) {
                    $grantorB = $grantorsBackup->where('id', $grantor['grantor_id'])->first();
                    $grantorS = Grantor::where('name', $grantorB['name'])->first();
                    
                    $procedureToInsert->grantors()->attach($grantorS->id);
                }

                //REGISTRATION PROCEDURE DATA
                $rgds = $registrationProcedureData->where('procedure_id', $procedure['id'])->values();
                foreach ($rgds as $rgd) {
                    $rgd['procedure_id'] = $procedureToInsert->id;
                    unset($rgd['id']);
                    RegistrationProcedureData::create($rgd);
                }

                //SHAPES Y GRANTOR SHAPE
                $shapeBackup = $shapes->where('procedure_id', $procedure['id'])->values();
                foreach ($shapeBackup as $shape) {
                    $shape['procedure_id'] = $procedureToInsert->id;
                    $grantorSh = $grantorShape->where('shape_id', $shape['id'])->values();

                    unset($shape['id']);
                    $shapeServer = Shape::create($shape);

                    foreach ($grantorSh as $grantor) {
                        $gantorAux = $grantorsBackup->where('id', $grantor['grantor_id'])->first();
                        $grantorServer = Grantor::where('name', $gantorAux['name'])->first();
                        $shapeServer->grantors()->attach($grantorServer->id, ['type' => $grantor['type'], 'principal' => $grantor['principal']]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            print_r('Procedure Backup Error: ' . $e->getMessage());
        }
    }
}
