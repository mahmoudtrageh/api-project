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
        'description',
        'price',
        'address',
        'longitude',
        'latitude',
        'rate'
    ];

    public function hotel_images()
    {
        return $this->belongsToMany(hotel_image::class, 'hotel_images', 'hotel_id', 'image');
    }

    public function hotel_facilities()
    {
        return $this->belongsToMany(hotel_facility::class, 'hotel_facilities', 'hotel_id', 'facility_id');
    }

    public static function getByDistance($lat, $lng, $distance)
      {
      $results = DB::select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) 
      * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) 
      AS distance FROM hotels HAVING distance < ' . $distance . ' ORDER BY distance') );

      return $results;
      }
}
