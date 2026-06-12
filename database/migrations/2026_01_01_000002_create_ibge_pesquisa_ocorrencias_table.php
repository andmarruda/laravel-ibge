<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibge_pesquisa_ocorrencias', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo_pesquisa', 50)->index();
            $table->integer('ano');
            $table->unsignedTinyInteger('mes')->default(0);
            $table->unsignedSmallInteger('ordem_periodo')->default(0);
            $table->string('nome_ocorrencia', 500)->nullable();
            $table->json('payload');
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();

            $table->unique(
                ['codigo_pesquisa', 'ano', 'mes', 'ordem_periodo'],
                'ibge_ocorrencias_identity_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibge_pesquisa_ocorrencias');
    }
};
