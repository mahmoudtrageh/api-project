<?php

namespace App\Http\Controllers\Api;

use App\Models\Hotel;
use App\Models\Booking;
use App\Models\User;
use App\Models\Facility;
use App\Models\hotel_image;
use App\Models\hotel_facility;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class HomeController extends Controller
{
    public function get_hotels()
    {
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            $hotel->hotel_images = hotel_image::where('hotel_id', $hotel->id)->get();
            $hotel->hotel_facilities = hotel_facility::where('hotel_id', $hotel->id)->get();
        }

        if (count($hotels) == 0) {
            $Result = [
                'status' => ['type' => '0'],
                'data' => $hotels
            ];
            return response()->json($Result);
        }
        $Result = [
            'status' => ['type' => '1'],
            'data' => $hotels
        ];
        return response()->json($Result);
    }

    public function search_hotels(Request $request){

        if($request->name) {
            $hotels = Hotel::where('name', 'like', '%' . $request->name . '%');
        }
          
        if($request->address) {
            $hotels = Hotel::where('address', $request->address);
        }

        if($request->max_price && $request->min_price || $request->address) {
            $hotels = Hotel::where('price', '>=', $request->min_price)->where('price', '<=',$request->max_price)->where('address', $request->address);
        }
          
        if($request->facilities) {
            $hotel_facilities = hotel_facility::whereIn('facility_id', $request->facilities)->pluck('hotel_id')->toArray();
            $hotels = Hotel::whereIn('id', $hotel_facilities);
        }

        if($request->latitude && $request->longitude && $request->distance) {

            $hotels_object = Hotel::getByDistance($request->latitude, $request->longitude, $request->distance);

            $ids = [];

            foreach($hotels_object as $q)
            {
                array_push($ids, $q->id);
            }

            $hotels = Hotel::whereIn('id', $ids);

        }
          
        $hotels = $hotels->paginate($request->count);
             
        $result = [
            'status' =>
                ['type' => '1'],
            'data'=>$hotels
        ];
        
        return response()->json($result);
          
      }

      public function book(Request $request)
      {
          $v = validator($request->all(), [
              'user_id' => 'required',
              'hotel_id' => 'required',
          ],
              [
                  'user_id.required' => 'enter user',
                  'hotel_id.required' => 'enter hotel',
              ]);
          if ($v->fails()) {
           $error = $v->errors()->first();
           if($error=='enter user')
           $error='?????????? ?????????? ????????????????';
          else if($error=='enter hotel')
           $error='?????????? ?????????? ????????????';
          else
          $error = '?????? ??????';
          
              $result = 
              [
                  'status' =>
                      ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]]
              ];
             
              return response()->json($result);
          }
          $hotel = Hotel::where('id', $request->hotel_id)->first();
          $user = User::where('id', $request->user_id)->first();

          if ($user != null && $hotel != null && $booking = Booking::create($request->all())) {
           
              $result = [
                  'status' =>
                      [
                      'type' => '1',
                       'title' => ['ar'=>'???? ?????????? ?????????? ??????????','en'=>'succefully create']
                      ]
              ];
          } else if ($hotel == null || $user == null) {
            $result = [
                'status' =>
                    [
                    'type' => '0',
                     'title' => ['ar'=>'???? ???????? ???????????? ???? ?????? ??????????????','en'=>'please make sure you add correct data']
                    ]
            ];
        } else {
              $result = [
                  'status' =>
                      [
                      'type' => '0',
                       'title' =>[
                         'ar'=>'?????? ?????? ?????????? ?????????? ??????????',
                         'en'=>'error cannot create'
                         ]
                      ]
              ];
          }
          return response()->json($result);
      }
  
      public function updateBookingStatus(Request $request)
      {
        $checker = Booking::where('id', $request->booking_id)->first();
        $input = $request->all();

          $v = validator($request->all(), [
                'booking_id' => 'required',
              'type' => 'required',
          ],
              [
                'booking.required' => 'enter booking',
                  'type.required' => 'enter type',
              ]);
          if ($v->fails()) {
           $error = $v->errors()->first();
           if($error=='enter type')
           $error='?????????? ?????????? ?????? ??????????';
            if($error=='enter booking')
           $error='?????????? ?????????? ??????????';
        
          else
          $error = '?????? ??????';
          
            $result = 
            [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]]
            ];
             
              return response()->json($result);
          }
         
          if ($checker == null) {

             $result = [
                  'status' =>
                      [
                      'type' => '1',
                       'title' => ['ar'=>'?????? ?????????? ?????? ??????????','en'=>'this booking is not exist']
                      ]
              ];

          } else if ($checker->update($input)) {
           
              $result = [
                  'status' =>
                      [
                      'type' => '1',
                       'title' => ['ar'=>'???? ?????????? ?????? ?????????? ??????????','en'=>'succefully updated']
                      ]
              ];
          
        } else {
              $result = [
                  'status' =>
                      [
                      'type' => '0',
                       'title' =>[
                         'ar'=>'?????? ?????? ?????????? ?????????? ??????????',
                         'en'=>'error cannot create'
                         ]
                      ]
              ];
          }
          return response()->json($result);
      }


    public function getBooking(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user_id)->where('type', $request->type)->paginate($request->count);

        if (count($bookings) == 0) {
            $Result = [
                'status' => ['type' => '0'],
                'data' => $bookings
            ];
            return response()->json($Result);
        }
        $Result = [
            'status' => ['type' => '1'],
            'data' => $bookings
        ];
        return response()->json($Result);
    }

    public function get_facilities()
    {
        $facilities = Facility::all();

        foreach ($facilities as $facility) {
            $facility->image = url('images/' . $facility->image);
        }

        if (count($facilities) == 0) {
            $Result = [
                'status' => ['type' => '0'],
                'data' => $facilities
            ];
            return response()->json($Result);
        }
        $Result = [
            'status' => ['type' => '1'],
            'data' => $facilities
        ];
        return response()->json($Result);
    }
      
    public function create_hotel(Request $request)
    {
        $v = validator($request->all(), [
            'name' => 'required|unique:hotels,name',
            'price' => 'required',
        ],
            [
                'name.required' => 'enter name',
                'price.required' => 'enter price',
            ]);
        if ($v->fails()) {
         $error = $v->errors()->first();
         if($error=='enter name')
         $error='?????????? ?????????? ??????????';
        else if($error=='enter price')
         $error='?????????? ?????????? ??????????';
        else
        $error = '?????? ??????';
        
            $result = 
            [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]]
            ];
           
            return response()->json($result);
        }

        $hotel = Hotel::create($request->all());
        $images = $request->images;
        $facilities = $request->facilities;

        $hotel->hotel_images()->sync($images);
        $hotel->hotel_facilities()->sync($facilities);

       

        if ($hotel->save()) {
         
            $result = [
                'status' =>
                    [
                    'type' => '1',
                     'title' => ['ar'=>'???? ?????????? ???????????? ??????????','en'=>'succefully create']
                    ]
            ];
      } else {
            $result = [
                'status' =>
                    [
                    'type' => '0',
                     'title' =>[
                       'ar'=>'?????? ?????? ?????????? ?????????? ??????????',
                       'en'=>'error cannot create'
                       ]
                    ]
            ];
        }
        return response()->json($result);
    }

    public function create_facility(Request $request)
    {
        $v = validator($request->all(), [
            'name' => 'required|unique:hotels,name',
        ],
            [
                'name.required' => 'enter name',
            ]);
        if ($v->fails()) {
         $error = $v->errors()->first();
         if($error=='enter name')
         $error='?????????? ?????????? ??????????';
        else
        $error = '?????? ??????';
        
            $result = 
            [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]]
            ];
           
            return response()->json($result);
        }

        if (facility::create($request->all())) {
         
            $result = [
                'status' =>
                    [
                    'type' => '1',
                     'title' => ['ar'=>'???? ?????????? ???????????? ??????????','en'=>'succefully create']
                    ]
            ];
      } else {
            $result = [
                'status' =>
                    [
                    'type' => '0',
                     'title' =>[
                       'ar'=>'?????? ?????? ?????????? ?????????? ??????????',
                       'en'=>'error cannot create'
                       ]
                    ]
            ];
        }
        return response()->json($result);
    }
}
