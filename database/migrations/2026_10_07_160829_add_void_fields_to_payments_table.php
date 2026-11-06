<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('void_reason', 255)
                ->nullable()
                ->after('status');

            $table->timestamp('voided_at')
                ->nullable()
                ->after('void_reason');
        });

        DB::table('payments')
            ->where('status', 'CANCELLED')
            ->update([
                'status' => 'VOIDED',
            ]);
    }

    public function down(): void
    {
        DB::table('payments')
            ->where('status', 'VOIDED')
            ->update([
                'status' => 'CANCELLED',
            ]);

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'void_reason',
                'voided_at',
            ]);
        });
    }
};
