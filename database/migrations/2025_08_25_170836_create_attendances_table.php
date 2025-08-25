<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('class_session_id')->constrained('class_sessions')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->enum('status', ['present','absent','late'])->default('present');
            $t->timestamp('marked_at')->nullable();
            $t->timestamps();

            $t->unique(['class_session_id','student_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('attendances');
    }
};
