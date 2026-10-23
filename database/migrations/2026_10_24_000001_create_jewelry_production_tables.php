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
        // 1. Production Products / Jobs
        Schema::create('production_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('Ring');
            $table->string('metal_type')->default('18K Yellow Gold');
            $table->decimal('target_metal_weight', 10, 3)->default(0);
            $table->boolean('is_master_piece_required')->default(false);
            $table->string('mold_id')->nullable();
            $table->string('image_url')->nullable();
            $table->string('status')->default('in_production'); // draft, in_production, qc_review, completed, cancelled
            $table->integer('current_stage_number')->default(1);
            
            // Financials & Cost Accumulators
            $table->decimal('metal_unit_price_per_g', 12, 2)->default(0);
            $table->decimal('total_metal_cost', 12, 2)->default(0);
            $table->decimal('total_stone_cost', 12, 2)->default(0);
            $table->decimal('total_labor_cost', 12, 2)->default(0);
            $table->decimal('total_expenses_cost', 12, 2)->default(0);
            $table->decimal('total_metal_loss_cost', 12, 2)->default(0);
            $table->decimal('total_manufacturing_cost', 12, 2)->default(0);
            $table->decimal('markup_percentage', 6, 2)->default(30.00);
            $table->decimal('recommended_selling_price', 12, 2)->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Production Stages / Lifecycle Steps
        Schema::create('production_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_product_id')->constrained('production_products')->onDelete('cascade');
            $table->integer('step_number'); // 1 to 15
            $table->string('step_name');
            $table->boolean('is_required')->default(true);
            $table->string('status')->default('pending'); // pending, in_progress, completed, skipped, failed
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('checkpoints_passed')->nullable();
            $table->json('stage_data')->nullable(); // flexible storage for stage-specific values (flask size, wax temp, etc.)
            $table->timestamps();

            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Gemstones Required / Allocated
        Schema::create('production_stones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_product_id')->constrained('production_products')->onDelete('cascade');
            $table->string('stone_type'); // Sapphire, Diamond, Emerald, Ruby, etc.
            $table->string('cut_shape')->default('Round Brilliant');
            $table->string('dimensions_mm')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('carat_weight', 8, 3)->default(0);
            $table->string('setting_method')->default('hand_prong'); // wax_cast, hand_prong, bezel, pave, channel
            $table->decimal('stone_unit_cost', 12, 2)->default(0);
            $table->decimal('total_stone_cost', 12, 2)->default(0);
            $table->boolean('is_set')->default(false);
            $table->timestamps();
        });

        // 4. Stage Specific Expenses (Labor, Consumables, Overhead)
        Schema::create('stage_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_stage_id')->constrained('production_stages')->onDelete('cascade');
            $table->foreignId('production_product_id')->constrained('production_products')->onDelete('cascade');
            $table->string('expense_type')->default('labor'); // labor, consumable, energy, plating, outside_service, other
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->date('expense_date')->nullable();
            $table->timestamps();
        });

        // 5. Precious Metal Logs (Input, Output, Scrap, Net Loss)
        Schema::create('stage_metal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_stage_id')->constrained('production_stages')->onDelete('cascade');
            $table->foreignId('production_product_id')->constrained('production_products')->onDelete('cascade');
            $table->string('metal_type');
            $table->decimal('input_weight_g', 10, 3)->default(0);
            $table->decimal('output_weight_g', 10, 3)->default(0);
            $table->decimal('scrap_recovered_g', 10, 3)->default(0);
            $table->decimal('dust_recovered_g', 10, 3)->default(0);
            $table->decimal('net_loss_g', 10, 3)->default(0);
            $table->decimal('loss_percentage', 6, 2)->default(0);
            $table->decimal('spot_price_per_g', 12, 2)->default(0);
            $table->decimal('loss_monetary_value', 12, 2)->default(0);
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_metal_logs');
        Schema::dropIfExists('stage_expenses');
        Schema::dropIfExists('production_stones');
        Schema::dropIfExists('production_stages');
        Schema::dropIfExists('production_products');
    }
};
