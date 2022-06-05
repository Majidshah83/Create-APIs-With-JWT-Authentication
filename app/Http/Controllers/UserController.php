<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Hash;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
class UserController extends Controller
{
    //register user
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|min:2|max:100',
            'email'=>'required|string|email|max:100|unique:users',
            'password'=>'required|string|min:6',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors(),400);
        }
        $result=new User();
        $result->name=$request->name;
        $result->email=$request->email;
        $result->password=Hash::make($request->password);
        $result->save();
        return response()->json([
          'message'=>'Register Successfully',
          'user'=>$result,
        ]);

    }
    // login user

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[

            'email'=>'required|string|email',
            'password'=>'required|string|min:6',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors(),400);
        }
          if (!$token=auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
      return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
          return response()->json([
              'access_token'=>$token,
              'token_type'=>'bearer',
              'expires_in'=>auth()->factory()->getTTL()*100,
          ]);
    }
    // user Profile
    public function profile()
    {

      return response()->json(auth()->user());
    }
    //token refresh
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    //logout function
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message'=>'User Logout Sucessfully',
        ]);
    }
}