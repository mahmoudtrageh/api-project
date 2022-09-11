<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'img'
    ];

    public function hotel_images()
    {
        return $this->hasMany(hotel_image::class);
    }

    public function hotel_facilities()
    {
        return $this->hasMany(hotel_facility::class);
    }

    public static function getByDistance($lat, $lng, $distance)
      {
      $results = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) 
      * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) 
      AS distance FROM hotels HAVING distance < ' . $distance . ' ORDER BY distance') );

      return $results;
      }
}
