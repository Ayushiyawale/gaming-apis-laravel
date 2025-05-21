<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Otp extends Model
{
    protected $fillable = ['mobile', 'otp', 'expires_at'];

    public $timestamps = true;

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
