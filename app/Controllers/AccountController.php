<?php

namespace App\Controllers;

class AccountController extends BaseController
{
    private function requireLogin()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
    {
        if ($redir = $this->requireLogin()) return $redir;

        return view('account/index');
    }

    public function bookings()
    {
        if ($redir = $this->requireLogin()) return $redir;

        return view('account/bookings');
    }
}
