<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('assignments', function (Blueprint $t) {
            $t->timestamp('open_at')->nullable()->after('description');
            $t->timestamp('close_at')->nullable()->after('open_at');
            $t->boolean('accept_late')->default(false)->after('close_at');
            $t->timestamp('late_until')->nullable()->after('accept_late');
        });
    }
    public function down(): void {
        Schema::table('assignments', function (Blueprint $t) {
            $t->dropColumn(['open_at','close_at','accept_late','late_until']);
        });
    }
};
