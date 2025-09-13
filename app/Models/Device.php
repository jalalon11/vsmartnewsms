<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'customer_id',
        'device_type_id',
        'brand',
        'model',
        'serial_number',
        'imei',
        'year',
        'color',
        'specifications',
        'accessories',
        'condition_notes',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function repairOrders(): HasMany
    {
        return $this->hasMany(RepairOrder::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }
}
