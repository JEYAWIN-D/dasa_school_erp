<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('school_settings', 'udise_no')) {
                $table->string('udise_no', 50)->nullable()->after('affiliation_no');
            }
            if (!Schema::hasColumn('school_settings', 'recognition_no')) {
                $table->string('recognition_no', 100)->nullable()->after('udise_no');
            }
            if (!Schema::hasColumn('school_settings', 'district')) {
                $table->string('district', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('school_settings', 'print_watermark_enabled')) {
                $table->boolean('print_watermark_enabled')->default(true)->after('online_payment_enabled');
            }
            if (!Schema::hasColumn('school_settings', 'print_watermark_text')) {
                $table->string('print_watermark_text', 255)->nullable()->after('print_watermark_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'udise_no',
                'recognition_no',
                'district',
                'print_watermark_enabled',
                'print_watermark_text',
            ]);
        });
    }
};

