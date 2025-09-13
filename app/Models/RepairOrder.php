<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RepairOrder extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'device_id',
        'service_id',
        'technician_id',
        'assigned_to',
        'created_by',
        'issue_description',
        'diagnosis',
        'solution',
        'status',
        'priority',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'estimated_completion',
        'actual_completion',
        'total_estimated_duration',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'customer_notes',
        'internal_notes',
    ];

    protected $casts = [
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'estimated_completion' => 'datetime',
        'actual_completion' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];



    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'repair_order_services')
            ->withPivot('service_price', 'estimated_duration', 'status', 'service_notes')
            ->withTimestamps();
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class, 'repair_order_parts')
            ->withPivot('quantity_used', 'unit_price', 'total_price')
            ->withTimestamps();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }



    /**
     * Get the assigned technician name (handles both regular technicians and admin users)
     */
    public function getAssignedTechnicianNameAttribute()
    {
        if ($this->technician) {
            return $this->technician->user->name;
        }

        // Check if this was assigned to an admin user
        if ($this->assigned_to && $this->assignedTo) {
            return $this->assignedTo->name . ' (Admin)';
        }

        return 'Unassigned';
    }

    /**
     * Get the technician ID for form selection (handles admin users)
     */
    public function getTechnicianSelectionIdAttribute()
    {
        if ($this->technician_id) {
            return $this->technician_id;
        }

        // Check if this was assigned to an admin user
        if ($this->assigned_to) {
            return 'admin-' . $this->assigned_to;
        }

        return null;
    }

    /**
     * Calculate total service cost from all services
     */
    public function calculateTotalServiceCost()
    {
        return $this->services()->sum('repair_order_services.service_price');
    }

    /**
     * Calculate total estimated duration from all services
     */
    public function calculateTotalEstimatedDuration()
    {
        return $this->services()->sum('repair_order_services.estimated_duration');
    }

    /**
     * Update the repair order totals based on services and parts
     */
    public function updateTotals()
    {
        $this->labor_cost = $this->calculateTotalServiceCost();
        $this->total_estimated_duration = $this->calculateTotalEstimatedDuration();
        $this->total_cost = $this->labor_cost + $this->parts_cost;

        // Update estimated completion based on total duration
        if ($this->total_estimated_duration > 0) {
            $this->estimated_completion = now()->addMinutes((int) $this->total_estimated_duration);
        }

        $this->save();
    }

    /**
     * Get formatted estimated duration
     */
    public function getFormattedEstimatedDurationAttribute()
    {
        if (!$this->total_estimated_duration) {
            return 'Not specified';
        }

        $hours = intval($this->total_estimated_duration / 60);
        $minutes = $this->total_estimated_duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Calculate progress percentage based on service completion
     */
    public function getProgressPercentageAttribute()
    {
        $totalServices = $this->services()->count();
        if ($totalServices === 0) {
            return 0;
        }

        $completedServices = $this->services()->wherePivot('status', 'completed')->count();
        return round(($completedServices / $totalServices) * 100);
    }

    /**
     * Check if the repair order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'in_progress', 'waiting_parts']);
    }

    /**
     * Check if the repair order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancel the repair order with reason
     */
    public function cancel(string $reason, bool $restoreParts = true): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        // Restore parts to inventory if requested
        if ($restoreParts) {
            $this->restorePartsToInventory();
        }

        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Restore allocated parts back to inventory
     */
    public function restorePartsToInventory(): void
    {
        foreach ($this->parts as $part) {
            $quantityUsed = $part->pivot->quantity_used;
            $part->increment('quantity_in_stock', $quantityUsed);
        }
    }

    /**
     * Get cancellation status information
     */
    public function getCancellationInfoAttribute(): ?array
    {
        if (!$this->isCancelled()) {
            return null;
        }

        return [
            'cancelled_at' => $this->cancelled_at,
            'reason' => $this->cancellation_reason,
            'cancelled_by' => $this->updated_at, // Could be enhanced to track who cancelled
        ];
    }
}
