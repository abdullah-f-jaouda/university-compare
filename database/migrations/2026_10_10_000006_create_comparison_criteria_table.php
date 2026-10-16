<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_criteria', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();       // معرّف ثابت يُستخدم بالكود: affordability, duration, practical_ratio...
            $table->string('name');                // الاسم المعروض للمستخدم
            $table->text('description')->nullable();

            // اتجاه التقييم: هل القيمة الأقل أفضل (التكلفة، المدة) أو الأعلى أفضل (نسبة العملي)؟
            $table->enum('direction', ['lower_is_better', 'higher_is_better']);

            $table->unsignedTinyInteger('default_weight')->default(1); // وزن افتراضي (1-5)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_criteria');
    }
};
