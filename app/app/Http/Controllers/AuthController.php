<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use ThrottlesLogins;
    
    function getLogin() {
        return view('pages.auth.login');
    }

    function doLogin(Request $request) {
        $this->validate($request, [
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        if ($this->limiter()->tooManyAttempts(strtolower($request->username).'|'.$request->ip(), 5, 1)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(strtolower($request->username).'|'.$request->ip());

            return redirect()->back()->withInput($request->except('password'))
                ->with('error', 'You have been locked out for ' + $seconds + ' seconds.');
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'status_id' => 
            \App\Status::where('name', 'Active')->first()->id], $request->remember_me))
        {
            $account = \App\Account::where('username', $request->username)->first();
            $account->last_login = \Carbon\Carbon::now();
            $account->save();

            $request->session()->regenerate();
            $this->limiter()->clear(strtolower($request->username).'|'.$request->ip());

            return redirect()->intended(route('dashboard.index'))->with('success', 'You are logged in.');
        }

        $account = \App\Account::where('username', $request->username)->first();
        if ($account !== NULL && \Illuminate\Support\Facades\Hash::check($request->password, $account->password))
        {
            if ($account->status()->first()->name == 'Inactive' || $account->trashed()) {
                return redirect()->back()->withInput($request->except('password'))
                    ->with('error', 'Account is blocked.');
            }

            return redirect()->back()->withInput($request->except('password'))
                ->with('error', 'Account is in invalid state.');
        }

        return redirect()->back()->withInput($request->except('password'))
            ->with('error', 'Username and password doesn\'t match.');
    }

    function getSignup() {
        return view('pages.auth.signup');
    }

    function doSignup(Request $request) {
        $this->validate($request, [
            'username' => ['required', 'string', 'between:4,50'],
            'password' => ['required', 'string', 'confirmed', 'min:4'],
            'email' => ['required', 'string', 'email', 'confirmed', 'max:255'],
            'name' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30'],
            'agree' => ['required'],
        ], [
            'agree.required' => 'You must agree to the terms and conditions.',
        ]);

        if (\App\Account::where('username', $request->username)->exists()) {
            return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                ->with('error', 'Username is already exist.');
        }

        if (\App\Account::where('email', $request->email)->exists()) {
            return redirect()->back()->withInput($request->except('email', 'email_confirmation', 'password', 'password_confirmation'))
                ->with('error', 'Email is already exist.');
        }

        try {         
            $account = new \App\Account;
            $account->username = $request->username;
            $account->password = bcrypt($request->password);
            $account->email = $request->email;
            $account->name = $request->name;
            $account->phone = $request->phone;

            $time = \Carbon\Carbon::now();
            $verification_token = bcrypt('T3P05'.$request->username.$time->timestamp.'T3P05');

            $account->verification_token = $verification_token;
            $account->verification_token_end = $time;

            $status = \App\Status::where('name', 'Active')->first();
            $account->status()->associate($status);

            $owner = new \App\Owner;
            $owner->save();
            $owner->accounts()->save($account);

            Mail::to($account->email)->send(new \App\Mail\Register($request->username, $verification_token));
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('auth.login'))->with('success', 'Successfully created account. Check email to verify.');
    }

    function getForgot() {
        return view('pages.auth.forgot');
    }

    function doForgot(Request $request) {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $account = \App\Account::where('email', $request->email)->where('child_type', 'Owner')->first();

        if ($account === NULL) {
            return redirect()->back()->with('error', 'Email is not found.');
        }

        try {
            $time = \Carbon\Carbon::now()->timestamp;

            $time = \Carbon\Carbon::now();
            $forgot_token = bcrypt('T3P05'.$request->email.$time->timestamp.'T3P05');

            $account->forgot_token = $forgot_token;
            $account->forgot_token_end = $time;
            $account->save();

            Mail::to($account->email)->send(new \App\Mail\Forgot($request->email, $forgot_token));
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('auth.login'))->with('success', 'We send an email if email is exist.');
    }

    function getReset(Request $request) {
        $account = \App\Account::where('email', $request->email)->where('child_type', 'Owner')->first();

        if ($account === NULL) {
            return redirect()->route('auth.login')->with('error', 'Data is not found.');
        }

        if ($account->forgot_token === NULL && $account->forgot_token_end === NULL) {
            return redirect()->route('auth.login')->with('error', 'Token is already used.');
        }

        $time = \Carbon\Carbon::createFromFormat('Y-n-j G:i:s', $account->forgot_token_end);

        if (!\Illuminate\Support\Facades\Hash::check('T3P05'.$account->email.$time->timestamp.'T3P05', $request->forgot_token) || $request->forgot_token != $account->forgot_token) {
            return redirect()->route('auth.login')->with('error', 'Data is invalid.');
        }

        if ($time->addDay()->timestamp <= \Carbon\Carbon::now()->timestamp) {
            $time = \Carbon\Carbon::now();
            $forgot_token = bcrypt('T3P05'.$account->email.$time->timestamp.'T3P05');

            $account->forgot_token = $forgot_token;
            $account->forgot_token_end = $time;
            $account->save();

            Mail::to($account->email)->send(new \App\Mail\Forgot($request->email, $forgot_token));
            return redirect()->route('auth.login')->with('error', 'Token is expired. We send new one.');
        }

        try {
            $account->forgot_token = NULL;
            $account->forgot_token_end = NULL;

            $password = random_str(15);
            $account->password = bcrypt($password);
            $account->save();

            Mail::to($account->email)->send(new \App\Mail\Reset($password));
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('auth.login')->with('error', 'Something wrong with the database.');
        }

        return redirect()->route('auth.login')->with('success', 'Check email. We send new password.');
    }

    function getVerify(Request $request) {
        $account = \App\Account::where('username', $request->username)->first();

        if ($account === NULL) {
            return redirect()->route('auth.login')->with('error', 'Data is not found.');
        }

        if ($account->verification_token === NULL && $account->verification_token_end === NULL) {
            return redirect()->route('auth.login')->with('success', 'You are already verified.');
        }
        
        $time = \Carbon\Carbon::createFromFormat('Y-n-j G:i:s', $account->verification_token_end);

        if (!\Illuminate\Support\Facades\Hash::check('T3P05'.$request->username.$time->timestamp.'T3P05', $request->verification_token) || $request->verification_token != $account->verification_token) {
            return redirect()->route('auth.login')->with('error', 'Data is invalid.');
        }

        if ($time->addDay()->timestamp <= \Carbon\Carbon::now()->timestamp) {
            $time = \Carbon\Carbon::now();
            $verification_token = bcrypt('T3P05'.$account->username.$time->timestamp.'T3P05');

            $account->verification_token = $verification_token;
            $account->verification_token_end = $time;
            $account->save();

            Mail::to($account->email)->send(new \App\Mail\Register($request->username, $verification_token));
            return redirect()->route('auth.login')->with('error', 'Token is expired. We send new one.');
        }

        try {
            $account->verification_token = NULL;
            $account->verification_token_end = NULL;
            $account->save();
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('auth.login')->with('error', 'Something wrong with the database.');
        }

        return redirect()->route('auth.login')->with('success', 'You are verified.');
    }

    function doLogout(Request $request) {
        Auth::logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->intended(route('auth.login'))->with('success', 'You are logged out.');
    }
    
}

