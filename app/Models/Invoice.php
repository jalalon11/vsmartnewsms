<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'repair_order_id',
        'customer_id',
        'issue_date',
        'subtotal',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'status',
        'payment_method',
        'due_date',
        'paid_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('amount_paid', '>', 0)
                    ->whereColumn('amount_paid', '<', 'total_amount');
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->total_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'text-green-400 bg-green-900/50 border-green-700',
            'partially_paid' => 'text-blue-400 bg-blue-900/50 border-blue-700',
            'pending' => 'text-yellow-400 bg-yellow-900/50 border-yellow-700',
            'overdue' => 'text-red-400 bg-red-900/50 border-red-700',
            'cancelled' => 'text-gray-400 bg-gray-900/50 border-gray-700',
            default => 'text-gray-400 bg-gray-900/50 border-gray-700',
        };
    }

    // Methods
    public function generateInvoiceNumber()
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastInvoice = static::whereYear('created_at', $year)
                           ->whereMonth('created_at', now()->month)
                           ->orderBy('id', 'desc')
                           ->first();

        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->total_amount = $this->subtotal - $this->discount_amount;
        return $this;
    }

    public function markAsPaid($paymentMethod = null, $paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_date' => $paymentDate ?? now(),
        ]);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function updateStatusBasedOnPayments()
    {
        $totalPaid = $this->amount_paid ?? 0;
        $totalAmount = $this->total_amount;

        if ($totalPaid >= $totalAmount) {
            $this->status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->status = 'partially_paid';
        } else {
            $this->status = 'pending';
        }

        $this->save();
        return $this;
    }

    public function addPayment($amount, $paymentMethod, $paymentDate = null, $notes = null, $referenceNumber = null)
    {
        // Create payment record
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => $paymentDate ?? now(),
            'notes' => $notes,
            'reference_number' => $referenceNumber,
        ]);

        // Update invoice totals
        $this->amount_paid = $this->payments()->sum('amount');
        $this->balance_due = $this->total_amount - $this->amount_paid;

        // Update status based on payment
        if ($this->balance_due <= 0) {
            $this->status = 'paid';
            $this->paid_date = $paymentDate ?? now();
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partially_paid';
        }

        $this->save();

        return $payment;
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    public function getPaymentProgressAttribute()
    {
        if ($this->total_amount <= 0) return 100;
        return ($this->amount_paid / $this->total_amount) * 100;
    }

    public function isFullyPaid()
    {
        return $this->status === 'paid' || $this->balance_due <= 0;
    }

    public function isPartiallyPaid()
    {
        return $this->status === 'partially_paid' && $this->amount_paid > 0 && $this->balance_due > 0;
    }
}
