<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    public const STATUSES = ['Available' => 'Available', 'In Use' => 'In Use', 'Maintenance' => 'Maintenance', 'Retired' => 'Retired', 'Lost' => 'Lost'];

    public const CONDITIONS = ['Good' => 'Good', 'Fair' => 'Fair', 'Damaged' => 'Damaged'];

    protected $fillable = ['asset_code', 'name', 'category', 'serial_number', 'company_id', 'location', 'assigned_to', 'purchase_date', 'purchase_cost', 'status', 'condition', 'notes'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'purchase_cost' => 'decimal:2'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
