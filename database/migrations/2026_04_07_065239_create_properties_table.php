<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('properties', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('type');
        $table->string('location');
        $table->decimal('rent_per_month', 10, 2)->default(0);
        $table->enum('status', ['Available', 'Occupied', 'Maintenance'])->default('Available');
        $table->string('image')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}
public function down()
{
    Schema::dropIfExists('properties');
}
};