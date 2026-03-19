<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerProfileController extends Controller
{
    /* -------------------- Page de Profie -------------------- */
    public function index()
    {
        return view('customer.profile');
    }
}
