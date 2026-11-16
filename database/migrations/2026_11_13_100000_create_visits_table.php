<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada carregamento de página do site público.
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor', 16)->index(); // hash de IP + navegador (conta visitantes únicos)
            $table->string('ip', 45)->nullable()->index();
            $table->string('user_agent', 512)->nullable();
            $table->string('browser', 40)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('os', 40)->nullable();
            $table->string('device', 10)->default('desktop'); // desktop · mobile · tablet · bot
            $table->boolean('is_bot')->default(false);
            $table->string('path')->default('/');
            $table->string('referrer')->nullable(); // domínio de origem
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['is_bot', 'created_at']);
        });

        // Cliques em links, botões e perguntas durante uma visita.
        Schema::create('visit_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->string('label', 120)->nullable();
            $table->string('href', 500)->nullable();
            $table->string('section', 60)->nullable();
            $table->string('element', 20)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_clicks');
        Schema::dropIfExists('visits');
    }
};
