<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedTinyInteger('credit_hours')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();   // رقم الفصل في الخطة الدراسية
            $table->enum('course_type', ['theoretical', 'practical', 'mixed'])->default('theoretical');

            $table->enum('data_status', ['verified', 'synthetic', 'unavailable'])->default('synthetic');

            $table->timestamps();

            $table->index(['program_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
