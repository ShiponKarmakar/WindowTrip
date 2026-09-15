<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::allWithDefaults();
        unset($settings['mail_password']); // never expose the password

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
            'mailPasswordSet' => (bool) Setting::get('mail_password'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'support_email' => ['required', 'email', 'max:120'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'office_address' => ['nullable', 'string', 'max:200'],
            'office_hours' => ['nullable', 'string', 'max:100'],
            'alert_email' => ['required', 'email', 'max:120'],
            'facebook_url' => ['nullable', 'url', 'max:200'],
            'instagram_url' => ['nullable', 'url', 'max:200'],
            'whatsapp_number' => ['nullable', 'string', 'max:40'],
            // Mail / SMTP
            'mail_mailer' => ['required', 'in:smtp,log'],
            'mail_host' => ['nullable', 'string', 'max:120'],
            'mail_port' => ['nullable', 'string', 'max:6'],
            'mail_username' => ['nullable', 'string', 'max:120'],
            'mail_password' => ['nullable', 'string', 'max:200'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,'],
            'mail_from_address' => ['nullable', 'email', 'max:120'],
            'mail_from_name' => ['nullable', 'string', 'max:100'],
        ]);

        // Store the SMTP password encrypted; keep existing if left blank.
        $password = $request->input('mail_password');
        unset($data['mail_password']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        if (filled($password)) {
            Setting::set('mail_password', Crypt::encryptString($password));
        }

        return back()->with('success', 'Settings saved.');
    }

    /** Send a test email using the current mail settings. */
    public function test(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            Mail::raw(
                "This is a test email from your Window Trip admin panel.\n\nIf you received this, your email settings are working correctly. ✅",
                fn ($m) => $m->to($data['test_email'])->subject('Window Trip — Test Email')
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with('success', 'Test failed: '.$e->getMessage());
        }

        $via = Setting::get('mail_mailer') === 'smtp' ? 'via SMTP' : '(captured to log — mailer is set to "log")';

        return back()->with('success', "Test email sent to {$data['test_email']} {$via}.");
    }
}
