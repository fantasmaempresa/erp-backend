<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Nette\Schema\Expect;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create copy of mysql dump for existing database';

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
        $this->info('Respaldando base de datos...');
        $dbConfig = config('database.connections.' . config('database.default'));

        // Generar nombre de archivo de respaldo
        $backupFileName = 'db-backup-notary' . date('Y-md-H-i') . '.sql';

        // Generar comando de mysqldump
        $dumpCommand = "mysqldump ";
        $dumpCommand .= "-u {$dbConfig['username']} ";
        $dumpCommand .= "-p{$dbConfig['password']} ";
        $dumpCommand .= "-h {$dbConfig['host']} ";
        $dumpCommand .= "-d {$dbConfig['database']} ";
        $dumpCommand .= "> {$backupFileName}";

        // Ejecutar el comando mysqldump
        exec($dumpCommand);

        // Comprobar si la copia de seguridad se realizó correctamente
        if (file_exists($backupFileName)) {
            $this->info("Copia de seguridad de la base de datos creada exitosamente: {$backupFileName}");
        } else {
            $this->error("Error al crear la copia de seguridad de la base de datos.");
        }
    }
}
