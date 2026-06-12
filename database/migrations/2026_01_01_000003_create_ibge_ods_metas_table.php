<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibge_ods_metas', function (Blueprint $table): void {
            $table->string('numero', 50)->primary();
            $table->unsignedTinyInteger('numero_objetivo')->index();
            $table->text('nome')->nullable();
            $table->text('descricao')->nullable();
            $table->json('payload');
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibge_ods_metas');
    }
};
