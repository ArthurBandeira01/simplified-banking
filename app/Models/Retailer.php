<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'cnpj'
    ];

    protected $hidden = [
        'password',
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
