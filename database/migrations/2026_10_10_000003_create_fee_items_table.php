<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();

            // فئة الرسم: تُستخدم لتحليل مساهمة كل فئة في فرق التكلفة بين برنامجين
            $table->enum('category', ['tuition', 'registration', 'laboratory', 'other']);
            $table->string('label')->nullable();               // وصف اختياري، مثلاً "رسوم نشاط طلابي"

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('LYD');

            // تكرار الدفع: يحدد كم مرة يُضرب الرسم في المدة الكاملة للدراسة
            $table->enum('frequency', ['one_time', 'per_semester', 'per_year']);

            $table->string('academic_year')->nullable();       // الفترة السارية، مثلاً 2026/2027

            $table->enum('data_status', ['verified', 'synthetic', 'unavailable'])->default('synthetic');
            $table->text('source_note')->nullable();

            $table->timestamps();

            $table->index(['program_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_items');
    }
};
