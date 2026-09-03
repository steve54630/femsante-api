<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trace chaque commande PayPal créée par le serveur, du prix réellement calculé
     * jusqu'à sa consommation par /user/update ou /user/register — pour ne jamais
     * accorder d'accès sans un paiement effectivement capturé et vérifié.
     */
    public function up(): void
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('email');
            $table->string('days'); // "30" / "90" / "365" / "A vie"
            $table->decimal('price', 8, 2); // prix calculé par le serveur, celui envoyé à PayPal
            $table->decimal('captured_amount', 8, 2)->nullable(); // montant réellement débité, à la capture
            $table->string('status')->default('created'); // created -> captured -> consumed
            $table->timestamps();

            $table->index(['email', 'days', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
