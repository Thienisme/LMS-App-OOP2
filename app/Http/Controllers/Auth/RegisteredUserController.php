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
            // Thêm validation cho các trường mới
            'class' => 'required|string|max:50',
            'student_code' => [
                'required',
                'string',
                'max:20',
                'unique:'.User::class, // Đảm bảo mã sinh viên là duy nhất
                'regex:/^[A-Za-z0-9]+$/' // Chỉ cho phép chữ và số
            ],
            'phone' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9]{10}$/' // Validate số điện thoại 10 số
            ],
        ], [
            // Custom error messages
            'student_code.unique' => 'This student code has already been taken.',
            'student_code.regex' => 'Student code can only contain letters and numbers.',
            'phone.regex' => 'Phone number must be 10 digits.',
        ]);

        // Bước 2: Xử lý file ảnh đại diện (nếu có)
        $profilePath = null;
        if ($request->hasFile('profile')) {
            // Tạo tên file duy nhất bằng timestamp và tên gốc
            $fileName = time() . '_' . $request->file('profile')->getClientOriginalName();
            $request->file('profile')->move(public_path('profile'), $fileName);
            $profilePath = $fileName;
        }

        // Bước 3: Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_img' => $profilePath ?? 'default-profile.png', // Sử dụng ảnh mặc định nếu không có upload
            'is_admin' => $request->role ?? 0, // Mặc định là user thường
            // Thêm các trường mới
            'class' => $request->class,
            'student_code' => strtoupper($request->student_code), // Chuyển mã sinh viên thành chữ hoa
            'phone' => $request->phone,
        ]);

        // Bước 4: Sự kiện đăng ký và đăng nhập người dùng
        event(new Registered($user));
        Auth::login($user);

        // Bước 5: Điều hướng đến trang chủ
        return redirect(RouteServiceProvider::HOME)->with('message', 'Registration successful!');
    }
}