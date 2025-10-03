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
        Schema::table('products', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['category_id']);
            
            // Add the foreign key constraint with cascade delete
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the cascade delete foreign key constraint
            $table->dropForeign(['category_id']);
            
            // Add back the original foreign key constraint without cascade delete
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories');
        });
    }
};