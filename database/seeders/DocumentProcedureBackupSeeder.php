<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\DocumentProcedure;
use App\Models\Procedure;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentProcedureBackupSeeder extends Seeder
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

        $import = new ImportCSV(Storage::path('backup_server/document_procedure_backup.csv'), delimeter: '|');
        $documentProcedures = $import->readFile();

        DB::beginTransaction();
        try {
            foreach ($documentProcedures as $documentProcedure) {
                $procedureBackup = $procedures->where('id', $documentProcedure['procedure_id'])->first();
                $procedureServer = Procedure::where('name', $procedureBackup['name'])->first();

                DocumentProcedure::create([
                    'document_id' => $documentProcedure['document_id'],
                    'procedure_id' => $procedureServer->id,
                    'file' => $documentProcedure['file'],
                ]);

                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            print_r('Docuement Procedure Backup Error: ' . $e->getMessage());
        }
    }
}
