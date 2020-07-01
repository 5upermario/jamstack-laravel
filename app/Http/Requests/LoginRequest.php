<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends ActionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            /** @var User $user */
            $user = User::where('email', $this->email)->first();

            if (!$user || !Hash::check($this->password, $user->password)) {
                $validator->errors()->add('email', 'The provided credentials are incorrect.');
            } else {
                $this->request->add(['user' => $user]);
            }
        });
    }
}
