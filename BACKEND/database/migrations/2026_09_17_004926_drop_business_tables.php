<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('asset_cars');
        Schema::dropIfExists('asset_pgas');
        Schema::dropIfExists('bpjs_records');
        Schema::dropIfExists('department_budgets');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('employee_rotations');
        Schema::dropIfExists('reliance_records');
        Schema::dropIfExists('wiphras');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Kosongkan atau definisikan ulang jika perlu rollback
    }
};