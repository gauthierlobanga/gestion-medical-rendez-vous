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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('date_of_birth');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->constrained('medecins')->nullOnDelete()->after('nom');
        });
        Schema::table('medecins', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->onDelete('cascade')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
