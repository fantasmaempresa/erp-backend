<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Models\Procedure;
use App\Models\ProcessingIncome;
use App\Models\Reminder;
use App\Traits\NotificationTrait;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class SendDailyReminders extends Command
{
    use NotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily Reminders';

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
        $now = Carbon::now()->format('Y-m-d');

        $reminders = Reminder::whereDate('expiration_date', '>=', now())->get();
        foreach ($reminders as $reminder) {
            $messageExtra = '';
            try {
                $days = $this->get_days($now, $reminder->expiration_date);

                if (($days == 10 || $days == 5 || $days <= 3) || $reminder->status == Reminder::$NO_NOTIFED) {
                    if ($reminder->type == Reminder::$PROCESSING_INCOME_CONFIG) {
                        $processingIncome =  ProcessingIncome::findOrFail($reminder->relation_id);
                        $messageExtra = ' - Ingreso (' . $processingIncome->name . ') - Expediente (' . $processingIncome->procedure->name . ')';
                    } else if ($reminder->type == Reminder::$PROCEDURE_CONFIG) {
                        $procedure = Procedure::findOrFail($reminder->relation_id);
                        $messageExtra = ' - Expediente (' . $procedure->name . ')';
                    }

                    $notification = $this->createNotification([
                        'title' => $reminder->name . ' ' . $messageExtra,
                        'message' => $reminder->message,
                    ]);

                    $this->sendNotification(
                        $notification,
                        null,
                        new NotificationEvent($notification, 0, 0, [])
                    );

                    $reminder->status = Reminder::$NOTIFED;
                    $reminder->save();
                    sleep(5);
                }
                if ($days == 0) {
                    $reminder->status = Reminder::$DISABLE;
                    $reminder->save();
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        Storage::put("tempJsonReminders.json", ['date' => $now]);

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
