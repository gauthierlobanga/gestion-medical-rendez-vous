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
        Schema::create('notification_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendezvous_id')->constrained('rendezvous')->onDelete('cascade');
            $table->enum('type_notification', ['confirmation', 'rappel_24h', 'rappel_1h', 'annulation', 'modification']);
            $table->string('destinataire');
            $table->string('sujet');
            $table->text('contenu');
            $table->timestamp('date_envoi')->nullable();
            $table->enum('statut', ['en_attente', 'envoye', 'erreur'])->default('en_attente');
            $table->text('erreur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_rendez_vous');
    }
};
