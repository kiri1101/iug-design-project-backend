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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('type_id')
                ->constrained('leave_types')
                ->cascadeOnUpdate();
            $table->text('cause');
            $table->string('departure');
            $table->string('return');
            $table->text('comment')->nullable();
            $table->enum('status', [1, 2, 3, 4, 5, 6, 7, 8])->comment('1 => pending, 2 => awaiting superior validation, 3 => awaiting hr validation, 4 => leave validated, 5 => Employee back, 6 => HR confirmed return, 7 => deadline overdue, 8 => Rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
