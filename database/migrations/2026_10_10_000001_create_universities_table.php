<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->default('بنغازي');
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();

            // للإيقاف بدل الحذف (deactivate) كما في متطلبات الإدارة
            $table->boolean('is_active')->default(true);

            // حالة البيانات: verified / synthetic / unavailable
            $table->enum('data_status', ['verified', 'synthetic', 'unavailable'])->default('synthetic');
            $table->text('source_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
