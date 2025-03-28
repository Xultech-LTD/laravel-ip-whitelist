<?php
namespace Xultech\LaravelIpWhitelist\Models;

use Illuminate\Database\Eloquent\Model;

class IpWhitelistEntry extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     * Will be resolved from config dynamically.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('ipwhitelist.table_prefix', '') . config('ipwhitelist.table', 'ip_whitelist_entries');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip', 'active'];
}