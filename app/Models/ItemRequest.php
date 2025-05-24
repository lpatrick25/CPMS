<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'employee_id',
        'item_id',
        'quantity',
        'release_quantity',
        'date_requested',
        'status',
        'approved_by_custodian',
        'approved_by_president',
        'released_by_custodian',
        'released_at',
    ];

    protected $casts = [
        'date_requested' => 'date',
        'released_at' => 'datetime',
    ];

    // Relationship with Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship with Item
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Relationship with Employee (user)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Relationship with custodian
    public function approvedByCustodian()
    {
        return $this->belongsTo(User::class, 'approved_by_custodian');
    }

    // Relationship with president
    public function approvedByPresident()
    {
        return $this->belongsTo(User::class, 'approved_by_president');
    }

    // Relationship with custodian who released
    public function releasedByCustodian()
    {
        return $this->belongsTo(User::class, 'released_by_custodian');
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
