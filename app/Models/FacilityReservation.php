<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'facility_id',
        'reservation_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'approved_by',
    ];

    // Relationship with Employee (user)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Relationship with Facility
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    // Relationship with approved user
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
