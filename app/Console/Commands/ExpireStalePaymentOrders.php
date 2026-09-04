<?php

namespace App\Console\Commands;

use App\Models\PaymentOrder;
use Illuminate\Console\Command;

class ExpireStalePaymentOrders extends Command
{
    protected $signature = 'payment-orders:expire {--minutes=60 : Âge minimal (en minutes) d’une commande "created" pour être expirée}';

    protected $description = 'Marque "expired" les commandes PayPal restées en "created" au-delà du délai donné — couvre les abandons que l’app ne peut pas signaler elle-même (crash, app tuée, réseau coupé).';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');

        $count = PaymentOrder::where('status', 'created')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->update(['status' => 'expired']);

        $this->info("$count commande(s) marquée(s) expired.");

        return self::SUCCESS;
    }
}
