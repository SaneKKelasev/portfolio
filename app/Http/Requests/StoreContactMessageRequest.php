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
            'name.required' => 'Укажите имя.',
            'name.max' => 'Имя не должно быть длиннее 120 символов.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Укажите корректный email.',
            'email.max' => 'Email не должен быть длиннее 254 символов.',
            'message.required' => 'Напишите сообщение.',
            'message.min' => 'Сообщение должно быть не короче 20 символов.',
            'message.max' => 'Сообщение не должно быть длиннее 3000 символов.',
            'privacy_consent.accepted' => 'Подтвердите согласие на обработку персональных данных.',
        ];
    }
}
