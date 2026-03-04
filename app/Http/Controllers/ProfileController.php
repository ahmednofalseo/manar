<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * عرض صفحة تعديل الحساب الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث بيانات الحساب الشخصي
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // إزالة الحقول التي لا يجب حفظها في قاعدة البيانات
        unset($data['current_password'], $data['password_confirmation']);

        // معالجة رفع الصورة الشخصية
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إن وجدت
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // رفع الصورة الجديدة
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        } else {
            // إذا لم يتم رفع صورة جديدة، احتفظ بالقيمة القديمة
            unset($data['avatar']);
        }

        // تحديث كلمة المرور إذا تم إدخالها
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // تحديث البيانات
        $user->update($data);

        return redirect()->route('profile.edit')
            ->with('success', 'تم تحديث بياناتك الشخصية بنجاح');
    }
}
