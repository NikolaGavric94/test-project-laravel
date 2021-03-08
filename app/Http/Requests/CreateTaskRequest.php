<?php

namespace App\Http\Requests;

use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->role === Constants::USER_ROLE_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'date_format:Y-m-d',
            'time' => 'date_format:H:i:s'
        ];
    }
}
