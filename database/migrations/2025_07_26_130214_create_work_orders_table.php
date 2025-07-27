<?php

use App\Enums\WorkOrderStatus;
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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Relacionamentos
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('user_id')->nullable()->constrained('users');

            // Status da OS (Ordem de Serviço)
            $table->string('status')->default(WorkOrderStatus::BUDGET->value);

            // Informações Descritivas
            $table->text('problem_reported')->nullable();
            $table->text('technical_diagnosis')->nullable();

            // Valores Totais (para acesso rápido e relatórios)
            $table->decimal('total_parts_price', 10, 2)->default(0);
            $table->decimal('total_services_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);

            // Datas
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
