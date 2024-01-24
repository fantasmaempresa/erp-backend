<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Procedure;
use App\Models\Shape;
use App\Models\TemplateShape;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Shape1Sedeer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('shapes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $import = new ImportCSV(Storage::path('backup_sicom/forma_1.csv'), delimeter: '|');
        $records = $import->readFile();
        $templateShape = TemplateShape::findOrFail(1);
        foreach ($records as $record) {
            try {

                $procedure = Procedure::where('name', trim($record['Expediente']))->first();
                if (empty($procedure->id)) {
                    print_r("salto -------> " . trim($record['Expediente']) . " \n");
                    continue;
                }

                $shape = new Shape();

                $shape->folio = empty($record['Folio']) ? 'bk' : $record['Folio'];
                $shape->notary = empty($record['Notario']) ? 'bk' : $record['Notario'];
                $shape->scriptures = empty($record['Escritura']) ? 'bk' : $record['Escritura'];
                $shape->property_account = empty($record['Cuenta']) ? 'bk' : $record['Cuenta'];
                $date = empty($record['FechaFirma'])
                    ? Carbon::today()
                    : Carbon::createFromFormat('d/m/Y', $record['FechaFirma'])->format('Y-m-d');
                $shape->signature_date = $date;
                $shape->departure = empty($record['Partida']) ? 'bk' : $record['Partida'];
                $shape->inscription = empty($record['Inscripcion']) ? 'bk' : $record['Inscripcion'];
                $shape->sheets = empty($record['Fojas']) ? 'bk' : $record['Fojas'];
                $shape->took = empty($record['Tomo']) ? 'bk' : $record['Tomo'];
                $shape->book = empty($record['Libro']) ? 'bk' : $record['Libro'];
//                Predio	Operacion	ValorCatastral
                $operation_value = empty($record['ValorOperacion']) ? 'bk' : $record['ValorOperacion'];
                $shape->operation_value = str_replace("$", "", $operation_value);
                $shape->description = empty($record['Descripcion']) ? 'bk' : $record['Descripcion'];
                $shape->total = empty($record['Total']) ? 'bk' : $record['Total'];
                $shape->data_form = [
                    'alienating_name' => $record['Nombre1'],
                    'alienating_street' => $record['Calle1'],
                    'alienating_outdoor_number' => $record['NoExt1'],
                    'alienating_interior_number' => $record['NoInt1'],
                    'alienating_colony' => $record['Colonia1'],
                    'alienating_locality' => $record['Localidad1'],
                    'alienating_municipality' => $record['Municipio1'],
                    'alienating_entity' => $record['Entidad1'],
                    'alienating_zipcode' => $record['CodPos1'],
                    'alienating_phone' => $record['Telefono1'],
                    'acquirer_name' => $record['Nombre2'],
                    'property' => $record['Predio'],
                    'value_catastral' => $record['ValorCatastral'],
                    'alienating_rfc' => $record['Rfc1'],
                    'alienating_crup' => $record['Curp1'],
                    'acquirer_rfc' => $record['Rfc2'],
                    'acquirer_curp' => $record['Curp2'],
                    'acquirer_street' => $record['Calle2'],
                    'acquirer_outdoor_number' => $record['NoExt2'],
                    'acquirer_interior_number' => $record['NoInt2'],
                    'acquirer_colony' => $record['Colonia2'],
                    'acquirer_locality' => $record['Localidad2'],
                    'acquirer_municipality' => $record['Municipio2'],
                    'acquirer_entity' => $record['Entidad2'],
                    'acquirer_zipcode' => $record['CodPos2'],
                    'acquirer_phone' => $record['Telefono2'],
                    'domicile' => $record['Domicilio'],
                    'alienated_property_street' => $record['Calle3'],
                    'alienated_property_outdoor_number' => $record['NoExt3'],
                    'alienated_property_interior_number' => $record['NoInt3'],
                    'alienated_property_colony' => $record['Colonia3'],
                    'alienated_property_locality' => $record['Localidad3'],
                    'alienated_property_municipality' => $record['Municipio3'],
                    'alienated_property_entity' => $record['Entidad3'],
                    'alienated_property_zipcode' => $record['CodPos3'],
                    'sold' => $record['Vendida'],
                    'remaining' => $record['Restante'],
                    'built' => $record['Construida'],
                    'use' => $record['Uso'],
                    'value_catastral' => $record['ValorCat'],
                    'rate' => $record['Tasa'],
                    'tax' => $record['Impuesto'],
                    'alienating_surcharges' => $record['Recargos1'],
                    'acquirer_surcharges' => $record['Recargos2'],
                    'total_tax' => $record['TotalImp'],
                    'alienating_basis' => $record['Ft1'],
                    'alienating_quote_basis' => $record['Fc1'],
                    'acquirer_basis' => $record['Ft2'],
                    'acquirer_quote_basis' => $record['Fc2'],
                    'alienated_property_basis' => $record['Ft3'],
                    'alienated_property_quote_basis' => $record['Fc3'],
                    'total_tax_rights' => $record['TotalDer'],
                    'reverse' => $record['Total'],
                ];
                $shape->reverse = '';
                $shape->template_shape_id = $templateShape->id;
                $shape->procedure_id = $procedure->id;
                $shape->save();

            } catch (IOException|ReaderNotOpenedException $e) {
                print_r('error al correr la migración ---> ', $e->getMessage());
            } catch (QueryException $exception) {
                print_r('error no ingresado --> ' . $exception->getMessage());
                exit();
            }
        }
    }
}
