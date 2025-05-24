<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_name', 'facility_description', 'facility_status', 'facility_in_charge',
    ];

    // Relationship with FacilityInCharge (user)
    public function facilityInCharge()
    {
        return $this->belongsTo(User::class, 'facility_in_charge');
    }

    // Relationship with FacilityReservations
    public function facilityReservations()
    {
        return $this->hasMany(FacilityReservation::class);
    }
}
