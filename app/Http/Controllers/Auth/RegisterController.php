<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    /**
     * Handle a registration request for the application
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        return fractal((new User)->register(...array_values($request->only(['email', 'password']))), new UserTransformer)
            ->respond();
    }

    /**
     * Check email exist before register
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function checkEmail(Request $request)
    {
        return response()->json(['exist' => User::where('email', $request->get('email'))->exists()]);
    }

    /**
     * Verify email address
     *
     * @param string $email_token
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function verifyEmail($email_token)
    {
        if (! $user = User::whereEmailToken($email_token)->first()) {
            throw new InvalidConfirmationCodeException;
        }

        $user->verify();

        return redirect()->route('root', ['verified' => 1]);
    }
}
