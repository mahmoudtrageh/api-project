<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hotel_facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'facility_id'
    ];

     public function hotel()
     {
         return $this->hasMany(Hotel::class);
     }

     public function facility()
     {
         return $this->hasMany(Facility::class);
     }
}
