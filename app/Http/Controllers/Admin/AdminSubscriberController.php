<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Mail\Websitemail;

class AdminSubscriberController extends Controller
{
    public function show()
    {
        $all_subscribers = Subscriber::where('status', 1)->get();
        return view('admin.subscriber_show', compact('all_subscribers'));
    }
    
}
