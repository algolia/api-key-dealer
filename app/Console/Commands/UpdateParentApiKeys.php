<?php

namespace App\Console\Commands;

use App\Config;
use App\Key;
use Illuminate\Console\Command;

class UpdateParentApiKeys extends Command
{
    protected $signature = 'dealer:update:parent-keys';

    protected $description = 'Update all parents in the database';

    public function handle()
    {
        $appIdsInDb = Key::all()->pluck('app_id')->toArray();
        $appIdsInConfig = collect(config('repositories'))->map(function ($c) {
            return $c['app-id'] ?? null;
        })->filter()->values()->toArray();

        $c = new Config([]);
        $defaultAppIds = [$c->getAppId(), $c->getCtsAppId(1), $c->getCtsAppId(2)];

        $appIds = array_unique(array_merge($appIdsInDb, $appIdsInConfig, $defaultAppIds));

        // All all missing keys to the DB
        $toCreate = array_values(array_diff($appIds, $appIdsInDb));
        foreach ($toCreate as $appId) {
            Key::create(['app_id' => $appId]);
        }

        // Update all keys expiring in the next 12 hours
        Key::where('expires_at', '<', time() + 43200) // 12 hours
            ->whereIn('app_id', $appIds)
            ->orWhere('expires_at', null)
            ->get()
            ->each(function (Key $key) {
                $key->updateKeys();
            })
        ;
    }
}
