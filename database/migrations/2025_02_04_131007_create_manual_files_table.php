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
        Schema::create('manual_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_id')->unsigned()->nullable()->constrained('manuals')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('filename');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_files');
    }
};
