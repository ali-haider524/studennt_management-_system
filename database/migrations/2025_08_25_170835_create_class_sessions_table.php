<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('class_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->date('session_date');
            $t->time('starts_at')->nullable();
            $t->time('ends_at')->nullable();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $t->enum('status', ['planned','open','closed'])->default('open');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('class_sessions');
    }
};
