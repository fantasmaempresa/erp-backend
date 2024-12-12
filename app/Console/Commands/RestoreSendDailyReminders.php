<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Reminder;

class RestoreSendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:restore-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a daily reminder';

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
        $lastNow = Storage::get("tempJsonReminders.json");
        $now = Carbon::now()->format('Y-m-d');

        $days = $this->get_days($lastNow, $now);

        if ($days > 0) {
            $reminders = Reminder::whereDate('expiration_date', '>=', now())->get();
            foreach ($reminders as $reminder){
                $reminder->status = Reminder::$NO_NOTIFED;
                $reminder->save();
            }
        }else {
            print_r("Seguimos en el mismo día");
        }

        return 0;
    }

    public function get_days($fecha_inicio, $fecha_fin)
    {
        // Convertir las fechas a objetos Carbon
        $fecha_inicio = Carbon::parse($fecha_inicio);
        $fecha_fin = Carbon::parse($fecha_fin);

        return $fecha_inicio->diffInDays($fecha_fin);
    }
}
