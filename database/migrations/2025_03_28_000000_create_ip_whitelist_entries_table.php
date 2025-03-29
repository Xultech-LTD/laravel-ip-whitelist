<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries'), function (Blueprint $table) {
            $table->id();
            $table->string('ip')->unique(); // supports exact IP, wildcard, CIDR
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries'));
    }
};
