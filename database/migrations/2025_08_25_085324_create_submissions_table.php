<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $t->string('file_path')->nullable();
            $t->text('notes')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->enum('status', ['pending','submitted','graded','late'])->default('pending');
            $t->string('grade')->nullable(); // letter or numeric
            $t->timestamps();

            $t->unique(['assignment_id','student_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
