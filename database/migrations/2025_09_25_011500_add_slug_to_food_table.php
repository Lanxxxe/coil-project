<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->unique('slug');
        });

        // Backfill existing rows
        $rows = DB::table('food')->select('id','name','slug')->get();
        foreach ($rows as $r) {
            $slug = $r->slug ?: Str::slug($r->name ?? '');
            if ($slug) {
                // Ensure uniqueness by appending id if needed
                $exists = DB::table('food')->where('slug', $slug)->where('id', '!=', $r->id)->exists();
                if ($exists) { $slug = $slug.'-'.$r->id; }
                DB::table('food')->where('id', $r->id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('food', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
