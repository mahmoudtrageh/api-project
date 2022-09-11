<?php

namespace App\Http\Controllers\Api;
use Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $v = Validator($request->all(), [
            'name' => 'bail|required|unique:users,name',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6|max:15',
            'password_confirmation' => 'required|same:password',
        ],
            [
                 'name.required' => 'Please Enter The Name',
                    'name.unique' => 'name already taken',
                    'password.required' => 'Please Enter The Password',
                    'password_confirmation.required' => 'Please Enter The Password Confirmation',
                    'email.required' => 'Please Enter The Email',
                    'email.unique'=>'email is taken',
                    'password.max' => 'Max Password Is 10 Characters',
                    'password.min' => 'Min Password Is 6 Characters',
                    'password_confirmation.same' => 'Password Confirmation Doesn`\t Match',
            ]);
        if ($v->fails()) {
               $error = $v->errors()->first();
	    if	($error == 'Please Enter The Name')
	    $error = 'من فضلك ادخل الاسم' ;
	   
	    else if($error == 'Please Enter The Password')
	    $error = 'من فضلك ادخل كلمة المرور' ;
	    else if($error == 'Please Enter The Password Confirmation')
	    $error = 'من فضلك ادخل تأكيد كلمة المرور' ;
	    else if($error == 'Please Enter The Email')
	    $error = 'من فضلك ادخل الايميل' ;
	    else if($error == 'Max Password Is 10 Characters')
	    $error = 'كلمة المرور لا تزيد عن 10 حروف' ;
	    else if($error == 'Min Password Is 6 Characters')
	    $error = 'كلمة المرور ليست اقل من 6 حروف' ;
	    else if($error == 'Password Confirmation Doesn`\t Match')
	    $error = 'كلمة المرور غير متطابقة' ;
	    else if($error == 'email is taken')
	    $error = 'هذا الايميل مستخدم من قبل' ;
	    else if($error == 'name already taken')
	    $error = 'هذا الاسم مستخدم من قبل';
	    else
	    $error = 'تأكد من بياناتك فضلاً' ;
	    
            $Result = [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]],

            ];
            return response()->json($Result);
        }
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $input['api_token'] = Str::random(60);
        if ($register = User::create($input)) {
            {
                $user = User::where('id', $register->id)->orderBy('id')->first();
                $user->img = url('images/' . $user->img);
                $Result = [
                    'status' =>
                        ['type' => '1', 'title' => ['ar'=>'تم تسجيل الحساب بنجاح','en'=>'user created successfuly']],
                    'data' => $user
                ];
            }
        } else {
            $Result = [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>'Error Occurred','ar'=>'حدث خطأ']],
            ];
        }
        return response()->json($Result);

    }

    public function login(Request $request)
    {
        $v = Validator($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Please Enter The email',
            'password.required' => 'Please Enter The Password',
        ]);
        if ($v->fails()) {
       $error = $v->errors()->first();
	    if	($error == 'Please Enter The email')
	    $error = 'من فضلك ادخل الايميل' ;
	   
	    else if($error == 'Please Enter The Password')
	    $error = 'من فضلك ادخل كلمة المرور' ;
	    else
	    $error = 'حدث خطأ';
	       
            $Result = [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]],
            ];
            return response()->json($Result);
        }
        if ($user = User::where('email', $request->email)->first()) {
            if(!Hash::check($request->password,$user->password)){
                 $Result = [
                    'status' =>
                        ['type' => '0', 'title' => ['ar'=>'كلمه المرور خطأ ', 'en'=>'wrong password']],
                ];
                return response()->json($Result);
            }
            
            $Result = [
                'status' =>
                        ['type' => '1', 'title' => ['ar'=>'تم تسجيل الدخول بنجاح','en'=>'signed in successfuly']],
                'data' => $user
            ];
            return response()->json($Result);
        }
        $Result = [
            'status' =>
                ['type' => '0', 'title' => ['en'=>'email or password is incorrect', 'ar'=>'تأكد من كلمة المرور او الايميل']],
        ];
        return response()->json($Result);

    }

    public function profile_info(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user == null) {
            $Result = [
                'status' => ['type' => '0'],
                'data' => $user
            ];
            return response()->json($Result);
        }

        $user->image = url('images/' . $user->image);
        $Result = [
            'status' => ['type' => '1'],
            'data' => $user
        ];
        return response()->json($Result);
    }

    public function pass_change(Request $request)
    {
        $v = validator($request->all(), [
            'password' => 'required|min:6|max:15',
            'password_confirmation' => 'required|same:password',
            'email' => 'required',
        ],
            [
                'password.required' => 'please enter password',
                'password_confirmation.required' => 'please enter password confirm',
                'password_confirmation.same' => 'Password confirmation isnt the same',
                'password.min' => 'min Password is 6 chars',
                'password.max' => 'Password not more than 15 chars',
                'email.required' => 'enter email',
            ]);
        if ($v->fails()) {
            $error = $v->errors()->first(); 
        if ($error == 'please enter password')
	    $error = 'برجاء إدخال كلمه المرور' ;
	   else if ($error == 'please enter password confirm')
	    $error = 'برجاء إدخال تأكيد كلمه المرور' ;
	   else if($error == 'min Password is 6 chars')
	    $error = 'كلمة المرور الجديدة لا تقل عن 6 حروف' ;
	else if($error == 'Password not more than 15 chars')
	    $error = 'كلمه المرور الجديده لا تزيد عن 15 حروف' ;
	else if($error == 'Password confirmation isnt the same')
	    $error = 'تأكيد كلمه المرور لا تتطابق' ;
	    else if($error == 'enter email')
	    $error = 'برجاء إدخال البريد الإلكترونى' ;
	 else
	     $error = 'حدث خطأ ما';
            $Result = [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]],
            ];
            return response()->json($Result);
        }
        if ($user = User::where('email', $request->email)->first()) {
            $user->update(['password' => bcrypt($request->password)]);
            $Result = [
                'status' =>
                    ['type' => '1', 'title' => ['en'=>'password changed successfully', 'ar'=>'تم تغيير كلمة المرور بنجاح']],
                'data' => $user
            ];
            return response()->json($Result);
        }
        $Result = [
            'status' =>
                ['type' => '0', 'title' => ['ar'=>'حدث خطأ , برجاء المحاوله ثانيه','en'=>'error occured, plz try again']],
        ];
        return response()->json($Result);
    }

    

    public function update_data(Request $request)
    {
        $token = $request->header('token');
        $checker = User::where('api_token', $token)->first();
        $input = $request->all();
        $v = validator($request->all(), [

            'name' => 'required|unique:users,name,' . $checker->id,
            'email' => 'required|unique:users,email,' . $checker->id,
        ],
        [
            'name.required' => 'please enter name',
            'name.unique' => 'name already exist',
            'email.required' => 'please enter email',
            'email.unique' => 'email already exist',

        ]);

        if ($v->fails()) {
                $error = $v->errors()->first(); 

        if ($error == 'please enter name')
            $error = 'برجاء إدخال الإسم' ;
           else if ($error == 'name already exist')
            $error = 'الإسم موجود بالفعل' ;
           else if($error == 'please enter email')
            $error = 'من فضلك أدخل البريد الإلكتروني' ;
            else if($error == 'email already exist')
            $error = 'البريد الإلكتروني مسجل بالفعل' ;
         else
             $error = 'حدث خطأ ما';

            $Result = [
                'status' =>
                    ['type' => '0', 'title' => ['en'=>$v->errors()->first(),'ar'=>$error]],
            ];
            return response()->json($Result);
        }
        
            if ($file = $request->file('image')) {
                @unlink(base_path('images/' . $checker->image));
                $name = rand(0000, 9999) . time() . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('/images'), $name);
                $input['image'] = $name;

            }
            if (!$file = $request->file('image')) {
                $input['image'] = $checker->image;
            }

        if ($checker->update($input)) {
            $Result = [
                'status' =>
                ['type' => '1', 'title' => ['ar'=>'تم تحديث البيانات بنجاح','en'=>'data updated successfully']],
                'data' => $checker
            ];
            return response()->json($Result);
        }

        $Result = [
            'status' =>
            ['type' => '0', 'title' => ['ar'=>'حدث خطأ , برجاء المحاوله ثانيه','en'=>'error occured, plz try again']],
        ];
        return response()->json($Result);
    }


}
