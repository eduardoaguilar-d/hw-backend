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
        Schema::create('reward_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('order_count')->comment('Número de pedidos necesarios para obtener esta recompensa');
            $table->decimal('discount_percentage', 5, 2)->comment('Porcentaje de descuento');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0)->comment('Orden de la recompensa (para saber cuál es la última)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_configs');
    }
};
