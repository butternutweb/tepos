<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    function getIndex() {
        return view('pages.profile.index')->with('account', Auth::user());
    }

    function doIndex(Request $request) {
        $this->validate($request, [
            'action' => ['required', Rule::in(['change-email', 'change-password', 'change-profile'])]
        ]);

        if ($request->action == 'change-email') {
            $this->validate($request, [
                'old_email' => ['required', 'string', 'email', 'max:255'],
                'new_email' => ['required', 'string', 'email', 'confirmed', 'max:255'],
                'password' => ['required', 'string', 'min:4']
            ], [
                'old_email.required' => 'The old email field is required.',
                'old_email.string' => 'The old email must be a string.',
                'old_email.email' => 'The old email must be a valid email address.',
                'old_email.max' => 'The old email may not be greater than :max characters.',
                'new_email.required' => 'The new email field is required.',
                'new_email.string' => 'The new email must be a string.',
                'new_email.email' => 'The new email must be a valid email address.',
                'new_email.confirmed' => 'The new email confirmation does not match.',
                'new_email.max' => 'The new email may not be greater than :max characters.'
            ]);

            $account = \App\Account::find(Auth::id());

            if (!\Illuminate\Support\Facades\Hash::check($request->password, $account->password)) {
                return redirect()->back()->withInput($request->except('password'))->with('error', 'Password is invalid.');
            }

            if (Auth::user()->email != $request->old_email) {
                return redirect()->back()->withInput($request->except('old_email', 'password'))->with('error', 'Old email is invalid.');
            }

            if ($request->old_email == $request->new_email) {
                return redirect()->back()->withInput($request->except('new_email', 'new_email_confirmation', 'password'))->with('error', 'Email could not be same.');
            }

            if (\App\Account::where('email', $request->new_email)->exists()) {
                return redirect()->back()->withInput($request->except('new_email', 'new_email_confirmation', 'password'))->with('error', 'New email is already exist.');
            }

            try {
                $time = \Carbon\Carbon::now();
                $changeemail_token = bcrypt('T3P05CH4NG3'.$request->old_email.$request->new_email.$time->timestamp.'T3P05CH4NG3');

                $account->changeemail_token = $changeemail_token;
                $account->changeemail_token_end = $time;
                $account->save();

                Mail::to($request->new_email)->send(new \App\Mail\ChangeEmail($request->old_email, $request->new_email, $changeemail_token));
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('password'))->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('profile.index'))->with('success', 'Check email for the next step.');
        } else if ($request->action == 'change-password') {
            $this->validate($request, [
                'old_password' => ['required', 'string', 'min:4'],
                'new_password' => ['required', 'string', 'confirmed', 'min:4'],
            ], [
                'old_password.required' => 'The old password field is required.',
                'old_password.string' => 'The old password must be a string.',
                'old_password.min' => 'The old password must be at least :min characters.',
                'new_password.required' => 'The new password field is required.',
                'new_password.string' => 'The new password must be a string.',
                'new_password.min' => 'The new password must be at least :min characters.',
                'new_password.confirmed' => 'The new password confirmation does not match.',
            ]);

            $account = \App\Account::find(Auth::id());

            if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $account->password)) {
                return redirect()->back()->withInput($request->except('old_password', 'new_password', 'new_password_confirmation'))->with('error', 'Password is invalid.');
            }

            if ($request->old_password == $request->new_password) {
                return redirect()->back()->withInput($request->except('old_password', 'new_password', 'new_password_confirmation'))->with('error', 'Password could not be same.');
            }
 
            try {
                $account->password = bcrypt($request->new_password);
                $account->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('old_password', 'new_password', 'new_password_confirmation'))->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('profile.index'))->with('success', 'You changed password.');
        } else if ($request->action == 'change-profile') {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:30'],
                'phone' => ['required', 'string', 'max:30'],
            ]);

            try {
                $account = \App\Account::find(Auth::id());
                $account->name = $request->name;
                $account->phone = $request->phone;
                $account->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('profile.index'))->with('success', 'You changed profile.');
        }

        return redirect()->back();
    }

    function getChange(Request $request) {
        $account = \App\Account::find(Auth::user()->id);

        if ($account->email != $request->old_email) {
            return redirect()->route('dashboard.index')->with('error', 'Data is invalid.');
        }

        if ($account->changeemail_token === NULL && $account->changeemail_token_end === NULL) {
            return redirect()->route('dashboard.index')->with('success', 'You are already changed email.');
        }

        $time = \Carbon\Carbon::createFromFormat('Y-n-j G:i:s', $account->changeemail_token_end);

        if (!\Illuminate\Support\Facades\Hash::check('T3P05CH4NG3'.$request->old_email.$request->new_email.$time->timestamp.'T3P05CH4NG3', $account->changeemail_token)) {
            return redirect()->route('dashboard.index')->with('error', 'Data is invalid.');
        }

        if ($time->addDay()->timestamp <= \Carbon\Carbon::now()->timestamp) {
            $time = \Carbon\Carbon::now();
            $changeemail_token = bcrypt('T3P05CH4NG3'.$request->old_email.$request->new_email.$time->timestamp.'T3P05CH4NG3');

            $account->changeemail_token = $changeemail_token;
            $account->changeemail_token_end = $time;
            $account->save();

            Mail::to($request->new_email)->send(new \App\Mail\ChangeEmail($request->old_email, $request->new_email, $changeemail_token));
            return redirect()->route('dashboard.index')->with('error', 'Token is expired. We send new one.');
        }

        try {
            $account->changeemail_token = NULL;
            $account->changeemail_token_end = NULL;
            $account->email = $request->new_email;
            $account->save();
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('dashboard.index')->with('error', 'Something wrong with the database.');
        }

        return redirect()->route('profile.index')->with('success', 'You changed email.');
    }
}
