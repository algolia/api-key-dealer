<?php

namespace App;

use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Key extends Model
{
    protected static $unguarded = true;

    private $acl = [
        'search', 'browse',
        'addObject', 'deleteObject',
        'listIndexes', 'deleteIndex',
        'settings', 'editSettings',
        'analytics',
        'logs',
    ];

    public static function findOrCreate($appId)
    {
        try {
            return self::get($appId);
        } catch (ModelNotFoundException $e) {
            return self::create([
                'app_id' => $appId,
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        self::created(function (Key $model) {
            $model->updateKeys();
        });
    }

    public static function get($appId)
    {
        return self::where('app_id', $appId)->firstOrFail();
    }

    public static function create($attributes)
    {
        $key = new self($attributes);
        $key->save();

        $key->updateKeys();

        return $key;
    }

    public function updateKeys()
    {
        $algolia = SearchClient::create($this->app_id, env($this->app_id.'_ADMIN'));

        $this->write = $algolia->addApiKey($this->acl)->wait()['key'];
        $this->search = $algolia->addApiKey(['search'])->wait()['key'];
        $this->expires_at = env('KEY_VALIDITY') + time();

        $this->update();

        return $this;
    }
}
