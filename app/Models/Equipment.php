<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'remaining_quantity',
        'has_serial',
        'equipment_in_charge',
    ];

    // Relationship with EquipmentSerial
    public function equipmentSerials()
    {
        return $this->hasMany(EquipmentSerial::class);
    }

    // Relationship with EquipmentBorrowing
    public function equipmentBorrowings()
    {
        return $this->hasMany(EquipmentBorrowing::class);
    }

    public function serials()
    {
        return $this->hasMany(EquipmentSerial::class, 'equipment_id');
    }

    public function equipmentInCharge()
    {
        return $this->belongsTo(User::class, 'equipment_in_charge');
    }

    public function inCharge()
    {
        return $this->belongsTo(User::class, 'equipment_in_charge');
    }
}
