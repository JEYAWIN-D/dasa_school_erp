<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'admission_nature')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('admission_nature', 20)->default('existing')->nullable()->index();
            });

            // Set all existing students to 'existing'
            \Illuminate\Support\Facades\DB::table('students')
                ->whereNull('admission_nature')
                ->orWhere('admission_nature', '')
                ->update(['admission_nature' => 'existing']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'admission_nature')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('admission_nature');
            });
        }
    }
};

