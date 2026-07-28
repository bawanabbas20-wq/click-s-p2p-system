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
        Schema::dropIfExists('purchase_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse dropping a table without recreating it
        // This is intentionally left empty as this is a destructive migration
    }
};
