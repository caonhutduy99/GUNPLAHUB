<?php

namespace App\Support;

class ValidationMessages
{
    /** Thông báo lỗi dùng chung (tiếng Việt) */
    public static function common(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute không hợp lệ.',
            'unique' => ':attribute đã tồn tại.',
            'numeric' => ':attribute phải là số.',
            'integer' => ':attribute phải là số nguyên.',
            'min' => ':attribute nhỏ hơn mức cho phép.',
            'max' => ':attribute vượt quá mức cho phép (ảnh tối đa 2MB).',
            'in' => ':attribute không hợp lệ.',
            'exists' => ':attribute không tồn tại.',
            'image' => ':attribute phải là tệp ảnh.',
            'mimes' => ':attribute phải có định dạng jpg, jpeg, png hoặc webp.',
            'regex' => ':attribute không đúng định dạng.',
        ];
    }
}
