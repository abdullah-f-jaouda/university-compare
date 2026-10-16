<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('discipline');                       // المجال: هندسة برمجيات، علوم حاسوب، إلخ
            $table->enum('degree_type', ['diploma', 'bachelor', 'master'])->default('bachelor');

            // مؤشرات أكاديمية للمقارنة (nullable = غير متوفر، ولا تُعامل كصفر)
            $table->decimal('duration_years', 3, 1)->nullable();
            $table->unsignedSmallInteger('total_credit_hours')->nullable();
            $table->unsignedTinyInteger('practical_percentage')->nullable(); // نسبة الجانب العملي 0-100

            // متطلبات القبول
            $table->decimal('min_admission_percentage', 5, 2)->nullable();
            $table->text('admission_notes')->nullable();

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->enum('data_status', ['verified', 'synthetic', 'unavailable'])->default('synthetic');
            $table->text('source_note')->nullable();

            $table->timestamps();

            $table->index(['university_id', 'discipline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
