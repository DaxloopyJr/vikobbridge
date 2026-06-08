<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanCosigner extends Model
{
    use HasFactory;

    protected $table = 'loan_cosigners';

    protected $fillable = [
        'loan_id', 'member_id', 'approved', 'approved_at',
        'rejected', 'rejected_at', 'notes',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'rejected' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function approve(): void
    {
        $this->update([
            'approved' => true,
            'approved_at' => now(),
            'rejected' => false,
            'rejected_at' => null,
        ]);
    }

    public function reject(?string $notes = null): void
    {
        $this->update([
            'rejected' => true,
            'rejected_at' => now(),
            'approved' => false,
            'approved_at' => null,
            'notes' => $notes,
        ]);
    }
}
