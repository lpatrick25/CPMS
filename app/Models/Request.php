<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_number', 'employee_id', 'date_requested', 'status'];

    protected $casts = [
        'date_requested' => 'date',
    ];

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
