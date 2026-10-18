<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Support\ValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:100'],
                'phone' => ['required', 'regex:/^(0|\+84)[0-9]{9}$/'],
                'message' => ['required', 'string', 'min:10', 'max:2000'],
            ],
            array_merge(ValidationMessages::common(), [
                'email.email' => 'Email không đúng định dạng.',
                'phone.regex' => 'Số điện thoại không hợp lệ (ví dụ: 0912345678).',
                'message.min' => 'Nội dung quá ngắn (tối thiểu 10 ký tự).',
                'message.max' => 'Nội dung quá dài (tối đa 2000 ký tự).',
            ]),
            [
                'name' => 'Họ và tên',
                'email' => 'Email',
                'phone' => 'Số điện thoại',
                'message' => 'Nội dung',
            ]
        );

        ContactMessage::create($data + ['user_id' => auth()->id()]);

        return redirect()->route('contact')
            ->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận được liên hệ và sẽ phản hồi sớm.');
    }
}
