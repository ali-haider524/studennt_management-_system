<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $t) {
    $t->id();
    $t->string('code')->unique();  // <-- must exist
    $t->string('title');
    $t->text('description')->nullable();
    $t->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
    $t->date('start_date')->nullable();
    $t->date('end_date')->nullable();
    $t->integer('capacity')->nullable();
    $t->enum('status', ['draft','active','completed','archived'])->default('draft');
    $t->timestamps();
     });

    }
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
