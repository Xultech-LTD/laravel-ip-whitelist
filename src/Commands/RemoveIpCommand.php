<?php
namespace Xultech\LaravelIpWhitelist\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveIpCommand extends Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'ipwhitelist:remove 
                            {ip : The IP address, CIDR block, or wildcard to remove}
                            {--store=database : Where to remove the IP from (database or config)}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Remove an IP address from the whitelist';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var \Illuminate\Console\Command $this */

        $ip = trim($this->argument('ip'));
        $store = $this->option('store');

        if ($store === 'config') {
            $this->warn("Config storage is read-only. Please remove this IP manually from your config/ipwhitelist.php file.");
            $this->line("Would have removed: <info>{$ip}</info>");
            return 0;
        }

        if ($store === 'database') {
            $table = config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries');

            if (!Schema::hasTable($table)) {
                $this->error("Table '{$table}' does not exist. Please run the migration first.");
                return 1;
            }

            $deleted = DB::table($table)->where('ip', $ip)->delete();

            if ($deleted) {
                $this->info("IP address <comment>{$ip}</comment> removed successfully.");
            } else {
                $this->warn("IP address <comment>{$ip}</comment> was not found.");
            }

            return 0;
        }

        $this->error("Unsupported storage method: {$store}");
        return 1;
    }
}