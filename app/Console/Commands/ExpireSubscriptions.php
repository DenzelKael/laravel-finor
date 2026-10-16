<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como vencidas (EXPIRED) las suscripciones activas cuya fecha de expiración haya pasado';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = today()->toDateString();

        $affected = Subscription::where('status', SubscriptionStatus::Active->value)
            ->whereDate('expiration_date', '<', $today)
            ->update([
                'status' => SubscriptionStatus::Expired->value,
            ]);

        $this->info("Se marcaron {$affected} suscripciones como vencidas.");

        return self::SUCCESS;
    }
}
