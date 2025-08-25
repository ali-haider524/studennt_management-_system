<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['enrolled','withdrawn','completed'])->default('enrolled');
            $t->string('grade')->nullable();
            $t->timestamps();

            $t->unique(['user_id','course_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
