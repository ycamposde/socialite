<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver) {
        return Socialite::driver($driver)->redirect();
    }

    public function handleProviderCallback($driver) {
      try {
        $user = Socialite::driver($driver)->user();
      } catch (\Exception $e) {
        return redirect()->route('login');
      }

      $existingUser = User::where('email', $user->email)->first();

      if ($existingUser) {
        auth()->login($existingUser, true);
      } else {
        $newUser                    = new User;
        $newUser->provider_name     = $driver;
        $newUser->provider_id       = $user->id;
        $newUser->name              = $user->name;
        $newUser->email             = $user->email;
        $newUser->email_verified_at = now();
        $newUser->avatar            = $user->avatar;
        $newUser->save();

        auth()->login($newUser, true);
      }

      return redirect($this->redirectPath());
    }

    public function handleProviderCallbackGoogle($driver) {
      try {
        $user = Socialite::driver($driver)->user();
      } catch (\Exception $e) {
        return redirect()->route('login');
      }

      $existingUser = User::where('email', $user->getEmail())->first();

      if ($existingUser) {
        auth()->login($existingUser, true);
      } else {
        $newUser                    = new User;
        $newUser->provider_name     = $driver;
        $newUser->provider_id       = $user->getId();
        $newUser->name              = $user->getName();
        $newUser->email             = $user->getEmail();
        $newUser->email_verified_at = now();
        $newUser->avatar            = $user->getAvatar();
        $newUser->save();

        auth()->login($newUser, true);
      }

      return redirect($this->redirectPath());
    }
}
