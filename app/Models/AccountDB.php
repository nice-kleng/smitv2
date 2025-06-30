<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDB extends Model
{
    protected $table = 'account_db';

    protected $fillable = [
        'app_name',
        'app_url',
        'username',
        'password',
        'user_id',
    ];

    public $timestamps = true; // Enable timestamps if needed

    /**
     * Get the user that owns the account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
