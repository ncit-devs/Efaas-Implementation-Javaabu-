<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $efaas_token = session('efaas_token');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($efaas_token) {
            return Socialite::driver('efaas')->logOut($efaas_token, url('/'));
        }

        return redirect('/');
    }


    /**
     * Redirect user to eFaas Login
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider() {

        return Socialite::driver('efaas')->redirect();
    }

    /**
     * Process the eFaas Callback
     * @param Request $request
     */
    public function handleProviderCallback(Request $request) {
        $efaas_user = Socialite::driver('efaas')->user();
        $access_token = $efaas_user->token;

        session('efaas_token', $access_token);

        // handle the process of creating or updating user here.


        // dumping the data to view for demo
        dd($efaas_user);
    }
}
