<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Part extends Model
{
    protected $fillable = [
        'part_number',
        'name',
        'description',
        'category',
        'device_id',
        'compatible_devices',
        'cost_price',
        'selling_price',
        'quantity_in_stock',
        'minimum_stock_level',
        'supplier',
        'is_active',
        'status',
        'order_date',
        'expected_arrival_date',
        'received_date',
        'status_notes',
    ];

    protected $casts = [
        'compatible_devices' => 'array',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'minimum_stock_level' => 'integer',
        'is_active' => 'boolean',
        'order_date' => 'date',
        'expected_arrival_date' => 'date',
        'received_date' => 'date',
    ];

    public function repairOrders(): BelongsToMany
    {
        return $this->belongsToMany(RepairOrder::class, 'repair_order_parts')
            ->withPivot('quantity_used', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->minimum_stock_level;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'ordered' => 'bg-yellow-900 text-yellow-300 border-yellow-600',
            'in_transit' => 'bg-blue-900 text-blue-300 border-blue-600',
            'in_stock' => 'bg-green-900 text-green-300 border-green-600',
            default => 'bg-gray-900 text-gray-300 border-gray-600',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'ordered' => 'Ordered',
            'in_transit' => 'In Transit',
            'in_stock' => 'In Stock',
            default => 'Unknown',
        };
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'ordered' => ['in_transit', 'in_stock'],
            'in_transit' => ['in_stock'],
            'in_stock' => ['ordered'], // Can reorder
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    public function updateStatus(string $newStatus, array $data = []): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $updateData = ['status' => $newStatus];

        // Set appropriate dates based on status
        switch ($newStatus) {
            case 'ordered':
                $updateData['order_date'] = $data['order_date'] ?? now();
                $updateData['expected_arrival_date'] = $data['expected_arrival_date'] ?? null;
                break;
            case 'in_transit':
                // Keep existing order_date
                break;
            case 'in_stock':
                $updateData['received_date'] = $data['received_date'] ?? now();
                break;
        }

        if (isset($data['status_notes'])) {
            $updateData['status_notes'] = $data['status_notes'];
        }

        return $this->update($updateData);
    }
}
