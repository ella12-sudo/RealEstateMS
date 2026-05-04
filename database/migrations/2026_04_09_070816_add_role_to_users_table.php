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
    Schema::table('users', function (Blueprint $table) {
        // This removes the old single 'name' column
        if (Schema::hasColumn('users', 'name')) {
            $table->dropColumn('name');
        }

        // This adds the new split columns
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
        
        // This handles the role and contact info
        $table->enum('role', ['admin', 'tenant'])->default('tenant')->after('email');
        
        // Use 'contact_number' instead of 'phone' to match your form
        $table->string('contact_number')->nullable()->after('role');
        $table->boolean('is_approved')->default(false)->after('contact_number');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'contact_number', 'is_approved']);
        });
    }
};

