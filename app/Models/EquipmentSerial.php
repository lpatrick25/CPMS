<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id', 'serial_number', 'status',
    ];

    // Relationship with Equipment
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    // Relationship with EquipmentBorrowingSerials
    public function equipmentBorrowingSerials()
    {
        return $this->hasMany(EquipmentBorrowingSerial::class);
    }
}
