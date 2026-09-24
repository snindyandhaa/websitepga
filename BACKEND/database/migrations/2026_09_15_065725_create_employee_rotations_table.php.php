<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_rotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('nik');
            $table->string('name');
            $table->date('mutation_date');
            $table->string('mutation_from_to');
            $table->string('status')->nullable();
            $table->string('division')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_rotations');
    }
};