<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('item_code', 50);
            $table->string('name');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
}; 