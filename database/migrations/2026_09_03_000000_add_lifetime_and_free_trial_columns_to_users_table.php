<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('USERS', function (Blueprint $table) {
            // Distingue un vrai achat "à vie" d'un compte qui n'a simplement jamais payé
            // (les deux cas ont VALID_DATE = null, ce qui était jusqu'ici ambigu).
            $table->boolean('LIFETIME_PURCHASED')->default(false)->after('VALID_DATE');

            // Posée une seule fois à l'activation de l'essai gratuit de 7 jours ;
            // sa seule présence empêche toute réactivation, même après expiration.
            $table->timestamp('FREE_TRIAL_USED_AT')->nullable()->after('LIFETIME_PURCHASED');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('USERS', function (Blueprint $table) {
            $table->dropColumn(['LIFETIME_PURCHASED', 'FREE_TRIAL_USED_AT']);
        });
    }
};
