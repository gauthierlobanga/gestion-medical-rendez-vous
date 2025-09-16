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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('numero_securite_sociale')->unique();
            $table->string('mutuelle')->nullable();
            $table->string('numero_mutuelle')->nullable();
            $table->text('antecedents_medicaux')->nullable();
            $table->text('allergies')->nullable();
            $table->text('traitements_chroniques')->nullable();
            $table->text('informations_urgence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
