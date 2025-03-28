<?php
namespace Xultech\LaravelIpWhitelist\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListIpCommand extends Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'ipwhitelist:list 
                            {--store=database : Where to list IPs from (database or config)}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'List all whitelisted IPs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var \Illuminate\Console\Command $this */


        $store = $this->option('store');

        if ($store === 'config') {
            $this->listFromConfig();
            return 0;
        }

        if ($store === 'database') {
            $this->listFromDatabase();
            return 0;
        }

        $this->error("Unsupported storage method: {$store}");
        return 1;
    }

    /**
     * List IPs from config file.
     */
    protected function listFromConfig(): void
    {
        /** @var \Illuminate\Console\Command $this */


        $ips = config('ipwhitelist.whitelisted_ips', []);

        if (config('ipwhitelist.allow_env_override')) {
            $envKey = config('ipwhitelist.env_key', 'IP_WHITELIST_OVERRIDE');
            $envIps = explode(',', env($envKey, ''));
            $envIps = array_map('trim', $envIps);
            $ips = array_merge($ips, array_filter($envIps));
        }

        $ips = array_unique(array_filter($ips));

        if (empty($ips)) {
            $this->info("No IPs defined in config.");
            return;
        }

        $this->table(['#', 'IP Address'], collect($ips)->map(fn ($ip, $i) => [$i + 1, $ip]));
    }

    /**
     * List IPs from the database.
     */
    protected function listFromDatabase(): void
    {
        /** @var \Illuminate\Console\Command $this */

        $table = config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries');

        if (!Schema::hasTable($table)) {
            $this->error("Table '{$table}' does not exist.");
            return;
        }

        $records = DB::table($table)
            ->select('ip', 'active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($records->isEmpty()) {
            $this->info("No IPs found in database.");
            return;
        }

        $this->table(['#', 'IP Address', 'Active', 'Created At'], $records->map(function ($record, $i) {
            return [
                $i + 1,
                $record->ip,
                $record->active ? 'Yes' : 'No',
                $record->created_at,
            ];
        }));
    }
}