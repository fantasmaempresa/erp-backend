<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class CompressAndMoveFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folders:compress-move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprime y mueve carpetas del storage a otra ruta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folders = [
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/clients/',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/clients_link',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/incomming',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/iso_documentation',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/procedures',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/registration_procedure_data',
            'C:/Users/mj_re/Documents/GIT/back/erp-backend/storage/app/user',
        ];

        // Get the destination path
        // $destinationPath = '/home/alex/';
        $destinationPath = 'C:\Users\mj_re\Documents\GIT\back\erp-backend';


        // Check if the destination path exists
        if (!file_exists($destinationPath)) {
            $this->error("La ruta de destino '{$destinationPath}' no existe.");
            return;
        }

        // Compress and move folders
        foreach ($folders as $folder) {
            if ($this->compressAndMoveFolder($folder, $destinationPath)) {
                $this->info('Las carpeta -> ' . $folder  . ' ha sido comprimida y movida exitosamente.');
            }else {
                $this->error('No se pudo compresionar o mover la carpeta -> ' . $folder);
            }
        }
    }

    /**
     * Comprime y mueve una carpeta.
     *
     * @param string $folder Nombre de la carpeta
     * @param string $destinationPath Ruta de destino
     */
    public function compressAndMoveFolder($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            $this->warn('No se pudo comprimir la carpeta');
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination . 'backup' . date('Y-m-d-H-i-s') . '.zip', ZIPARCHIVE::CREATE)) {
            $this->warn('No se pudo crear el archivo ZIP');
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $this->info('Comienza compresión de directorio');

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);


                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $this->info('Comienza compresión de archivo');

            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}
