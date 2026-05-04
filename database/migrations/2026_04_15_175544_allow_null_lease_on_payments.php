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
        Schema::table('payments', function (Blueprint $table) {
            // This allows the lease_id to be empty (null) in the database
            $table->unsignedBigInteger('lease_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // This reverts it back to being mandatory if you ever rollback
            $table->unsignedBigInteger('lease_id')->nullable(false)->change();
        });
    }
};