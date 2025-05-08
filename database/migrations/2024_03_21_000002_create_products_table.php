<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('product_suppliers')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->longText('introduction')->nullable();
            $table->longText('specification')->nullable();
            $table->text('shipping_method')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'product_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('products');
    }
}; 