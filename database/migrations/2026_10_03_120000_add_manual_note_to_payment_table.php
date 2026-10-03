<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan wajib untuk settle/pembatalan manual agar terlihat
     * kenapa pembayaran dilunasi manual (cash / koreksi / dll).
     */
    public function up()
    {
        Schema::table('payment', function (Blueprint $table) {
            if (! Schema::hasColumn('payment', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('payment', 'payment_settle_by')) {
                $table->string('payment_settle_by', 100)->nullable()->after('payment_note');
            }
        });
    }

    public function down()
    {
        Schema::table('payment', function (Blueprint $table) {
            if (Schema::hasColumn('payment', 'payment_settle_by')) {
                $table->dropColumn('payment_settle_by');
            }
            if (Schema::hasColumn('payment', 'payment_note')) {
                $table->dropColumn('payment_note');
            }
        });
    }
};
