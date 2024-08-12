<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Config;
use Auth;

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
        $this->middleware('guest:admin')->except('logout');
        $this->middleware('guest:mahasiswa')->except('logout');
        $this->middleware('guest:dosen')->except('logout');
        $this->middleware('guest:dekan')->except('logout');
        $this->middleware('guest:rektorat')->except('logout');
    }

    public function showAdminLoginForm()
    {
        return view('auth.homepage', [
            'url' => Config::get('constants.guards.admin')
        ]);
    }

    public function showMahasiswaLoginForm()
    {
        return view('auth.homepage', [
            'url' => Config::get('constants.guards.mahasiswa')
        ]);
    }

    public function showDosenLoginForm()
    {
        return view('auth.homepage', [
            'url' => Config::get('constants.guards.dosen')
        ]);
    }

    public function showDekanLoginForm()
    {
        return view('auth.homepage', [
            'url' => Config::get('constants.guards.dekan')
        ]);
    }

    public function showRektoratLoginForm()
    {
        return view('auth.homepage', [
            'url' => Config::get('constants.guards.rektorat')
        ]);
    }

    protected function validator(Request $request)
    {
        return $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
    }

    protected function guardLogin(Request $request, $guard)
    {
        $this->validator($request);

        return Auth::guard($guard)->attempt(
            [
                'email' => $request->email,
                'password' => $request->password
            ],
            $request->get('remember')
        );
    }

    public function adminLogin(Request $request)
    {
        if ($this->guardLogin($request, Config::get('constants.guards.admin'))) {
            return redirect()->intended('/admin');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function mahasiswaLogin(Request $request)
    {
        if ($this->guardLogin($request,Config::get('constants.guards.mahasiswa'))) {
            return redirect()->intended('/mahasiswa');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function dosenLogin(Request $request)
    {
        if ($this->guardLogin($request,Config::get('constants.guards.dosen'))) {
            return redirect()->intended('/dosen');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function dekanLogin(Request $request)
    {
        if ($this->guardLogin($request,Config::get('constants.guards.dekan'))) {
            return redirect()->intended('/dekan');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    public function rektoratLogin(Request $request)
    {
        if ($this->guardLogin($request,Config::get('constants.guards.rektorat'))) {
            return redirect()->intended('/rektorat');
        }

        return back()->withInput($request->only('email', 'remember'));
    }
}
