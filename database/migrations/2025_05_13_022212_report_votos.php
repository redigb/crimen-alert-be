<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up(): void {
        Schema::create('report_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('report_id')->constrained('users_reports');
            $table->enum('vote', ['conforme', 'no_conforme']);
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'report_id']); // Un usuario solo puede votar una vez por reporte
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_votes');
    }
};
