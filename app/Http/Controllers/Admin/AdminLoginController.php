<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use App\Models\Admin;
use Hash;
use Auth;

class AdminLoginController extends Controller
{
     /* -------------------- Page de connexion -------------------- */
    public function index()
    {
        // $pass = Hash::make('12345678');
       
        return view('admin.login');
    }
    
    /* -------------------- Soumission du formulaire de connexion -------------------- */
    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin_home');
        } else {
            return back()->with('error', 'Incorrect email or password.');
        }
    }

    /* -------------------- Déconnexion -------------------- */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin_login');
    }
    
    
    /* -------------------- Page mot de passe oublié -------------------- */
    public function forget_password()
    {
        return view('admin.forget_password');
    }

    /* -------------------- Soumission du formulaire de mot de passe oublié -------------------- */
    public function forget_password_submit(Request $request)
    {
        // Validation du formulaire
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Récupérer l'admin
        $admin_data = Admin::where('email', $request->email)->first();

        if (!$admin_data) {
            return back()->with('error', 'Email not found.');
        }

        // Générer un token sécurisé (32 octets aléatoires convertis en hexadécimal)
        $token = bin2hex(random_bytes(32));

        // Mettre à jour le token directement
        $admin_data->update(['token' => $token]);

        // Créer le lien de réinitialisation
        $reset_link = url('admin/reset-password/' . $token . '/' . $request->email);

        // Message et sujet de l'email
        $subject = "Password Reset Request";
        $message = "To reset your password, please click on the link below:<br>";
        $message .= "<a href='" . $reset_link . "'>Click Here</a>";

        // Envoyer l'email
        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        // Retourner avec un message de succès
        return redirect()->route('admin_login')
            ->with('success', 'Please check your email and follow the link to reset your password.');
    }

    /* -------------------- Page de réinitialisation de mot de passe -------------------- */
    public function reset_password($token, $email)
    {
        $admin_data = Admin::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$admin_data) {
            return redirect()->route('admin_login')->with('error', 'Invalid token or email.');
        }

        return view('admin.reset-password', compact('token', 'email'));
    }

    /* -------------------- Soumission du formulaire de réinitialisation -------------------- */
    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => ['required', 'min:6'],
            'retype_password' => ['required', 'same:password'],
        ]);

        $admin_data = Admin::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$admin_data) {
            return redirect()->route('admin_login')->with('error', 'Invalid token or email.');
        }

        // Réinitialiser le mot de passe
        $admin_data->update([
            'password' => Hash::make($request->password),
            'token' => null,
        ]);

        return redirect()->route('admin_login')->with('success', 'Password reset successfully. You can now log in.');
    }
}
