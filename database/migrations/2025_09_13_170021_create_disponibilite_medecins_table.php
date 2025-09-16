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
        Schema::create('disponibilite_medecins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('jour_semaine')->nullable(); // 1-7 (Lundi-Dimanche)
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->date('date_specifique')->nullable();
            $table->boolean('est_exception')->default(false);
            $table->string('raison_exception')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilite_medecins');
    }
};
