<?php

namespace App\Models;

use App\Enums\Boolean;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'label',
        'short',
        'file',
        'rtl',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', Boolean::YES);
    }
}
