<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tipe jadwal (LATIHAN / EVENT) untuk pewarnaan kalender publik.
     * EVENT = biru, selain itu = hijau. Tidak perlu edit file lagi.
     */
    public function up()
    {
        Schema::table('jadwal', function (Blueprint $table) {
            if (! Schema::hasColumn('jadwal', 'jadwal_type')) {
                $table->string('jadwal_type', 20)->default('LATIHAN')->after('jadwal_category_id');
            }
        });
    }

    public function down()
    {
        Schema::table('jadwal', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal', 'jadwal_type')) {
                $table->dropColumn('jadwal_type');
            }
        });
    }
};
