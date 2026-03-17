<?php

namespace App\Http\Controllers\Admin;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPageController extends Controller
{
      // about
    public function about()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_about', compact('page_data'));
    }

    public function about_update(Request $request)
    {
        $obj = Page::where('id', 1)->first();

        $obj->about_heading = $request->about_heading;
        $obj->about_content = $request->about_content;
        $obj->about_status = $request->about_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // terms
    public function terms()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_terms', compact('page_data'));
    }

    public function terms_update(Request $request)
    {
        $obj = Page::where('id', 1)->first();

        $obj->terms_heading = $request->terms_heading;
        $obj->terms_content = $request->terms_content;
        $obj->terms_status = $request->terms_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // privacy policy
    public function privacy()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_privacy', compact('page_data'));
    }

    public function privacy_update(Request $request)
    {
        
        $request->validate([
         'privacy_heading' => 'required|string|max:255',
         'privacy_content' => 'required|string',
         'privacy_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->privacy_heading = $request->privacy_heading;
        $obj->privacy_content = $request->privacy_content;
        $obj->privacy_status = $request->privacy_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }

    // contact
    public function contact()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_contact', compact('page_data'));
    }

    public function contact_update(Request $request)
    {
        
        $request->validate([
         'contact_heading' => 'required|string|max:255',
         'contact_map' => 'required|string',
         'contact_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->contact_heading = $request->contact_heading;
        $obj->contact_map = $request->contact_map;
        $obj->contact_status = $request->contact_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // Photo Gallery
    public function photo_gallery()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_photo_gallery', compact('page_data'));
    }

    public function photo_gallery_update(Request $request)
    {
        
        $request->validate([
         'photo_gallery_heading' => 'required|string|max:255',
         'photo_gallery_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->photo_gallery_heading = $request->photo_gallery_heading;
        $obj->photo_gallery_status = $request->photo_gallery_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // Video Gallery
    public function video_gallery()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_video_gallery', compact('page_data'));
    }

    public function video_gallery_update(Request $request)
    {
        
        $request->validate([
         'video_gallery_heading' => 'required|string|max:255',
         'video_gallery_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->video_gallery_heading = $request->video_gallery_heading;
        $obj->video_gallery_status = $request->video_gallery_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // Faq
    public function faq()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_faq', compact('page_data'));
    }

    public function faq_update(Request $request)
    {
        
        $request->validate([
         'faq_heading' => 'required|string|max:255',
         'faq_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->faq_heading = $request->faq_heading;
        $obj->faq_status = $request->faq_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // Blog
    public function blog()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_blog', compact('page_data'));
    }

    public function blog_update(Request $request)
    {
        
        $request->validate([
         'blog_heading' => 'required|string|max:255',
         'blog_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->blog_heading = $request->blog_heading;
        $obj->blog_status = $request->blog_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }


    // Cart
    public function cart()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_cart', compact('page_data'));
    }

    public function cart_update(Request $request)
    {
        
        $request->validate([
         'cart_heading' => 'required|string|max:255',
         'cart_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->cart_heading = $request->cart_heading;
        $obj->cart_status = $request->cart_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }
    // Checkout
    public function checkout()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_checkout', compact('page_data'));
    }

    public function checkout_update(Request $request)
    {
        
        $request->validate([
         'checkout_heading' => 'required|string|max:255',
         'checkout_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->checkout_heading = $request->checkout_heading;
        $obj->checkout_status = $request->checkout_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }
    // Payment
    public function payment()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_payment', compact('page_data'));
    }

    public function payment_update(Request $request)
    {
        
        $request->validate([
         'payment_heading' => 'required|string|max:255',
        //  'checkout_status'  => 'required|in:0,1',
        ]);

        
        
        $obj = Page::where('id', 1)->first();

        $obj->payment_heading = $request->payment_heading;
        // $obj->payment_status = $request->payment_status;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }
}
