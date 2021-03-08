<?php
namespace App\Http\Response;

class HttpResponse
{
    public static function response($message, $data = [], $code = 200) {
        return [
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];
    }
}
