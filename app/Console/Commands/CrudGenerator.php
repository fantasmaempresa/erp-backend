<?php

/*
 * CODE
 * Class CrudGenerator
 */

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @access  public
 *
 * @version 1.0
 */
class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator
    {name : Class (singular) for example User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     *
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->controller($name);
        $this->model($name);

        File::append(
            base_path('routes/api.php'),
            'Route::resource(\''.Str::plural(lcfirst($name))
            ."', '{$name}\{$name}Controller', ['except' => ['create', 'edit']]);\n"
        );

        $date = date("Y-m-d H:i:s");
        $dateFormat = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format(
            'Y_m_d_His'
        );

        $migrationFileName = $dateFormat."_create_".$this->toSnakeCase($name)
            ."_table";

        $this->call("make:migration", [$migrationFileName]);
    }

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
     * @param $type
     *
     * @return false|string
     */
    protected function getStub($type): bool|string
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    /**
     * @param $name
     */
    protected function model($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);
    }

    /**
     * @param $name
     */
    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $name,
                lcfirst(Str::plural($name)),
                lcfirst($name),
            ],
            $this->getStub('Controller')
        );

        $path = app_path("Http/Controllers/{$name}");

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        file_put_contents(
            $path."/{$name}Controller.php",
            $controllerTemplate
        );
    }

    /**
     * @param  $input
     *
     * @return string
     */
    private function toSnakeCase($input): string
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $matchString = strval($match);

            if (strcmp($matchString, strtoupper($matchString)) === 0) {
                $match = strtolower($match);
            } else {
                $match = lcfirst($match);
            }
        }

        return implode('_', $ret);
    }

}
