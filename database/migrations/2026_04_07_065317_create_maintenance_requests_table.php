<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('property_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('issue');
            $table->enum('category', [
                'Plumbing',
                'Electrical',
                'HVAC',
                'Structural',
                'Other',
            ])->default('Other');
            $table->enum('priority', [
                'Urgent',
                'High',
                'Medium',
                'Low',
            ])->default('Medium');
            $table->enum('status', [
                'Open',
                'In Progress',
                'Resolved',
            ])->default('Open');
            $table->text('description')->nullable();
            $table->date('resolved_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};