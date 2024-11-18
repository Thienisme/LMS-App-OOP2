<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
        {
            // Bước 1: Xác thực dữ liệu đầu vào
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'profile' => [
                    'nullable', 
                    File::types(['png', 'jpg', 'jpeg', 'gif'])->max(2048),
                ],
            ]);

            // Bước 2: Xử lý file ảnh đại diện (nếu có)
            $profilePath = null;
            if ($request->hasFile('profile')) {
                // Lưu file vào thư mục 'public/profile' và lấy tên gốc
                $fileName = $request->file('profile')->getClientOriginalName();
                $request->file('profile')->move(public_path('profile'), $fileName);

                // Chỉ lưu tên ảnh vào cơ sở dữ liệu (không lưu đường dẫn)
                $profilePath = $fileName;
            }

            // Bước 3: Tạo người dùng mới
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_img' => $profilePath, // Lưu chỉ tên ảnh
                'is_admin' => $request->role,
            ]);

            // Bước 4: Sự kiện đăng ký và đăng nhập người dùng
            event(new Registered($user));
            Auth::login($user);

            // Bước 5: Điều hướng đến trang chủ
            return redirect(RouteServiceProvider::HOME);
        }

}
