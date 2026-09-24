<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nik')->nullable();
            $table->date('mutation_date')->nullable();
            $table->string('on_to_mutation')->nullable();
            $table->string('ktp')->nullable();
            $table->string('level')->nullable();
            $table->string('status')->nullable();
            $table->string('division')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('last_promotion')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->date('join_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};