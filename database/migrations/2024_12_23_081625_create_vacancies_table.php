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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->integer('positions');
            $table->decimal('remuneration', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'closed', 'commissioned'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
