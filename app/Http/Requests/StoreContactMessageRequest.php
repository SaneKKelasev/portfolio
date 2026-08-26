<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreContactMessageRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:254',
            ],
            'message' => [
                'required',
                'string',
                'min:20',
                'max:3000',
            ],
            'privacy_consent' => [
                'accepted',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => 'Подтвердите согласие на обработку персональных данных.',
        ];
    }
}
