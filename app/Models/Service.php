<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'estimated_duration',
        'category',
        'device_type_id',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'estimated_duration' => 'integer',
        'is_active' => 'boolean',
    ];

    public function repairOrders(): HasMany
    {
        return $this->hasMany(RepairOrder::class);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }
}
