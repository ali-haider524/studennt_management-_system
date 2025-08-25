<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->timestamp('due_at')->nullable();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // teacher/admin
            $t->enum('visibility', ['draft','published','closed'])->default('published');
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
