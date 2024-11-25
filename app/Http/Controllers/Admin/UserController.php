<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{
    // Get User Data
    public function index()
    {
        try{
            return Inertia::render('Admin/User-management', [
                'users' => User::all()->map(function ($user){
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_img' => $user->profile_img,
                        'is_admin' => $user->is_admin,
                        'class' => $user->class, // Thêm trường lớp
                        'student_code' => $user->student_code, // Thêm mã sinh viên
                        'phone' => $user->phone, // Thêm số điện thoại
                        'created_at' => \Carbon\Carbon::parse($user->created_at)->format('d/m/Y - H:i:s'),
                        'updated_at' => \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y - H:i:s'),
                    ];
                })
            ]);
        }catch(\Exception $error){
            return $error->getMessage();
        }
    }

    // Update User Data Role
    public function update(Request $request, $id)
    {
        try{
          Validator::make($request->all(), [
              'is_admin' => ['required'],
              'class' => ['nullable', 'string', 'max:50'], // Không bắt buộc
              'student_code' => ['nullable', 'string', 'max:20', 'unique:users,student_code,'.$id],
              'phone' => ['nullable', 'string', 'max:15'],
          ])->validate();
        

            $user = User::find($id);

            if($user){
                $user->update($request->all());

                return redirect()->back()
                    ->with('message', 'Updated user information successfully.');
            }

        }catch(\Exception $error){
            return $error->getMessage();
        }
    }

    // Delete User
    public function destroy($id)
    {
        $user = User::find($id);

        if($user){
            $user->delete();
            return redirect()->back()
                ->with('message', 'User deleted successfully.');
        }
    }
}