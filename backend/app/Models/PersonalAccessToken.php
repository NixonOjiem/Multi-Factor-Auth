<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken // <-- Don't extend Sanctum's model
{
    /**
     * We must specify the connection, or it will use the default.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'personal_access_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_type', // <-- Need this for morphTo
        'tokenable_id',   // <-- Need this for morphTo
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the anscestor model that the token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }
}
