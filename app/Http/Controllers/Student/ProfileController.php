<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $student->load(['class', 'major', 'faculty']);
        return view('student.profile', compact('student'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        $student = Auth::user()->student;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($student->avatar) {
                Storage::disk('public')->delete($student->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $student->update(['avatar' => $path]);
        }

        return back()->with('success', 'Cập nhật ảnh đại diện thành công');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công');
    }
}
