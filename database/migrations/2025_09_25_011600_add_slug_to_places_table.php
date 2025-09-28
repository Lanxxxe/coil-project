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
        Schema::table('places', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->unique('slug');
        });

        $rows = DB::table('places')->select('place_id','name','slug')->get();
        foreach ($rows as $r) {
            $slug = $r->slug ?: Str::slug($r->name ?? '');
            if ($slug) {
                $exists = DB::table('places')->where('slug', $slug)->where('place_id', '!=', $r->place_id)->exists();
                if ($exists) { $slug = $slug.'-'.$r->place_id; }
                DB::table('places')->where('place_id', $r->place_id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
