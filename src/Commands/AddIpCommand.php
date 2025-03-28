<?php

namespace Xultech\LaravelIpWhitelist\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddIpCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'ipwhitelist:add 
                            {ip : The IP address, CIDR block, or wildcard to whitelist}
                            {--store=database : Where to store the IP (database)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new IP address to the whitelist';


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var \Illuminate\Console\Command $this */

        $ip = trim($this->argument('ip'));
        $store = $this->option('store');

        // Basic IP format check
        if (! $this->isValidIpFormat($ip)) {
            $this->error("Invalid IP format. Must be a valid IP, CIDR (e.g. 192.168.0.0/24), or wildcard (e.g. 192.168.*.*)");
            return 1;
        }

        // Handle config store (no real write)
        if ($store === 'config') {
            $this->warn("Config storage is read-only. Please manually add this IP to your config/ipwhitelist.php file.");
            $this->line("Would have added: <info>{$ip}</info>");
            return 0;
        }

        // Handle database store
        if ($store === 'database') {
            $table = config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries');

            if (!Schema::hasTable($table)) {
                $this->error("Table '{$table}' does not exist. Please run the migration first.");
                return 1;
            }

            // Prevent duplicates
            $exists = DB::table($table)->where('ip', $ip)->exists();
            if ($exists) {
                $this->warn("The IP <comment>{$ip}</comment> already exists in the whitelist.");
                return 0;
            }

            DB::table($table)->insert([
                'ip' => $ip,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("IP address <comment>{$ip}</comment> added successfully.");
            return 0;
        }

        $this->error("Unsupported storage method: {$store}");
        return 1;
    }

    /**
     * Check if the given IP format is valid (basic check for IP, wildcard, or CIDR).
     *
     * @param string $ip
     * @return bool
     */
    protected function isValidIpFormat(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (Str::contains($ip, '*')) {
            return true;
        }

        if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}\/[0-9]{1,2}$/', $ip)) {
            return true;
        }

        return false;
    }
}