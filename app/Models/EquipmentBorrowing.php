<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentBorrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'employee_id',
        'equipment_id',
        'quantity',
        'date_of_usage',
        'date_of_return',
        'status',
        'approved_by',
        'released_by',
        'returned_by',
        'released_at',
        'returned_at',
    ];

    // Relationship with Equipment
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    // Relationship with Employee (user)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Relationship with approved user
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relationship with released user
    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    // Relationship with returned user
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Relationship with EquipmentBorrowingSerials
    public function borrowingSerials()
    {
        return $this->hasMany(EquipmentBorrowingSerial::class, 'borrowing_id');
    }
}
