<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['facility', 'service'])->default('facility'); // مرفق (معمل، مكتبة) أو خدمة (نقل، إرشاد)
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);

            $table->enum('data_status', ['verified', 'synthetic', 'unavailable'])->default('synthetic');
            $table->text('source_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
