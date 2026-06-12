<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibge_pesquisas', function (Blueprint $table): void {
            $table->string('codigo', 50)->primary();
            $table->string('nome', 500)->nullable();
            $table->string('nome_ingles', 500)->nullable();
            $table->string('situacao', 100)->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('periodicidade_coleta', 100)->nullable();
            $table->string('periodicidade_divulgacao', 100)->nullable();
            $table->json('payload');
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibge_pesquisas');
    }
};
