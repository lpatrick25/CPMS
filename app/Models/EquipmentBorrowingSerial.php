<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentBorrowingSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'serial_id',
    ];

    // Relationship with EquipmentBorrowing
    public function equipmentBorrowing()
    {
        return $this->belongsTo(EquipmentBorrowing::class, 'borrowing_id');
    }

    // Relationship with EquipmentSerial
    public function equipmentSerial()
    {
        return $this->belongsTo(EquipmentSerial::class, 'serial_id');
    }

    public function serial()
    {
        return $this->belongsTo(EquipmentSerial::class, 'serial_id');
    }
}
