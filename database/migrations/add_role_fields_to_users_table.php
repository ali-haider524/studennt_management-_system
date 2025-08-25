<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->enum('role', ['admin','teacher','student','alumni'])
              ->default('student')->after('password');
            $t->string('phone')->nullable()->after('email');
            $t->boolean('is_active')->default(true)->after('role');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['role','phone','is_active']);
        });
    }
};


?>