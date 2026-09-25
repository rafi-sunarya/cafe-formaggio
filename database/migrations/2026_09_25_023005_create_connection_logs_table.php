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
        Schema::create('connection_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel gateways
            $table->foreignId('gateway_id')
                ->constrained('gateways')
                ->cascadeOnDelete();

            // Status koneksi: online atau offline
            $table->string('status');

            // Waktu respons dalam milidetik
            $table->unsignedInteger('response_time')->nullable();

            // Pesan error jika koneksi gagal
            $table->text('error_message')->nullable();

            // Waktu pengecekan koneksi
            $table->timestamp('checked_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connection_logs');
    }
};