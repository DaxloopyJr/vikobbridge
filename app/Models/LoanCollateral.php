<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanCollateral extends Model
{
    use HasFactory;

    protected $table = 'loan_collaterals';

    protected $fillable = [
        'loan_id', 'description', 'estimated_value',
        'document_path', 'verified', 'verified_at',
        'verified_by', 'verification_notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
