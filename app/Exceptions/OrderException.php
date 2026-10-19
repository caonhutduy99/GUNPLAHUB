<?php

namespace App\Exceptions;

use RuntimeException;

/** Lỗi nghiệp vụ khi đặt hàng / hủy đơn. Thông báo được hiển thị trực tiếp cho khách. */
class OrderException extends RuntimeException
{
    public function __construct(string $message, public ?string $field = null)
    {
        parent::__construct($message);
    }
}
