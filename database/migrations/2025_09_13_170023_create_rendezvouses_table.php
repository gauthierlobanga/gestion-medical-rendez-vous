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
        Schema::create('rendezvous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->datetime('date_heure');
            $table->integer('duree')->default(30); // en minutes
            $table->enum('statut', ['planifie', 'confirme', 'annule', 'termine', 'absent'])->default('planifie');
            $table->text('motif');
            $table->text('notes')->nullable();
            $table->enum('type_consultation', ['premiere', 'suivi', 'urgence', 'teleconsultation'])->default('premiere');
            $table->decimal('prix_consultation', 8, 2);
            $table->enum('mode_paiement', ['especes', 'carte', 'cheque', 'virement', 'autre'])->nullable();
            $table->boolean('est_paye')->default(false);
            $table->timestamps();

            $table->index('date_heure');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendezvous');
    }
};
