<?php
namespace V587ygq\OAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Client extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_clients';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'confidential' => 'bool',
        'revoked' => 'bool',
    ];

    /**
     * Set the client secret.
     *
     * @param  string  $value
     * @return void
     */
    public function setSecretAttribute($value)
    {
        $this->attributes['secret'] = Hash::make($value);
    }

    /**
     * Get the user that the client belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('oauth2.user_model'));
    }

    /**
     * Get all of the authentication codes for the client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auth_codes()
    {
        return $this->hasMany('V587ygq\OAuth\Models\AuthCode', 'client_id');
    }

    /**
     * Get all of the tokens that belong to the client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function access_tokens()
    {
        return $this->hasMany('V587ygq\OAuth\Models\AccessToken', 'client_id');
    }
}
