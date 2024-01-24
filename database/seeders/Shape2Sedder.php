<?php

namespace Database\Seeders;

use App\Imports\ImportCSV;
use App\Models\Procedure;
use App\Models\Shape;
use App\Models\TemplateShape;
use Box\Spout\Common\Exception\IOException;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class Shape2Sedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $import = new ImportCSV(Storage::path('backup_sicom/forma_2.csv'), delimeter: '|');
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

                    'type' => $record['Tipo'],
                    'index' => $record['Indice'],
                    'property' => $record['Inmueble'],
                    'rfc' => $record['Rfc'],
                    'curp' => $record['Curp'],
                    'alienating_home' => $record['Domicilio1'],
                    'acquirer_home' => $record['Domicilio2'],
                    'alienated_property_home' => $record['Domicilio3'],
                    'first_article' => $record['Articulo1'],
                    'legal_section' => $record['Apartado1'],
                    'first_fraction' => $record['Fraccion1'],
                    'first_quote' => $record['Cuota1'],
                    'firs_amount' => $record['Importe1'],
                    'second_article' => $record['Articulo2'],
                    'second_section' => $record['Apartado2'],
                    'second_fraction' => $record['Fraccion2'],
                    'second_quote' => $record['Cuota2'],
                    'second_amount' => $record['Importe2'],
                    'thirty_article' => $record['Articulo3'],
                    'thirty_section' => $record['Apartado3'],
                    'thirty_fraction' => $record['Fraccion3'],
                    'thirty_quote' => $record['Cuota3'],
                    'thirty_amount' => $record['Importe3'],
                    'fourth_article' => $record['Articulo4'],
                    'fourth_section' => $record['Apartado4'],
                    'fourth_fraction' => $record['Fraccion4'],
                    'fourth_quote' => $record['Cuota4'],
                    'fourth_amount' => $record['Importe4'],
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
