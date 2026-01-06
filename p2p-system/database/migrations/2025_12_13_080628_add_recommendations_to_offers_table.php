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
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('is_procurement_recommended')->default(false)->after('is_chosen');
            $table->text('procurement_recommendation_reason')->nullable()->after('is_procurement_recommended');
            $table->boolean('is_finance_recommended')->default(false)->after('procurement_recommendation_reason');
            $table->text('finance_recommendation_reason')->nullable()->after('is_finance_recommended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'is_procurement_recommended', 
                'procurement_recommendation_reason',
                'is_finance_recommended',
                'finance_recommendation_reason'
            ]);
        });
    }
};
