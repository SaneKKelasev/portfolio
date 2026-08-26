<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function authenticate(): void
    {
        if (! Auth::attempt($this->validated(), true)) {
            throw ValidationException::withMessages([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        $this->session()->regenerate();
    }
}
