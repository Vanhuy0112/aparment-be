<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('clients.login');
    }
    public function postLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('home');
        }

        return redirect()->back()->with('msg', 'Email hoặc mật khẩu không chính xác');

    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
