<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailSettingsServiceProvider extends ServiceProvider
{
    /**
     * Apply admin-managed SMTP settings over the default mail config at runtime.
     */
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $mailer = Setting::get('mail_mailer', 'log');

            if ($mailer === 'smtp' && Setting::get('mail_host')) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => Setting::get('mail_host'),
                    'mail.mailers.smtp.port' => (int) Setting::get('mail_port', 587),
                    'mail.mailers.smtp.username' => Setting::get('mail_username') ?: null,
                    'mail.mailers.smtp.password' => self::password(),
                    'mail.mailers.smtp.encryption' => Setting::get('mail_encryption') ?: null,
                ]);
            } elseif ($mailer === 'log') {
                config(['mail.default' => 'log']);
            }

            if (Setting::get('mail_from_address')) {
                config(['mail.from.address' => Setting::get('mail_from_address')]);
            }
            if (Setting::get('mail_from_name')) {
                config(['mail.from.name' => Setting::get('mail_from_name')]);
            }
        } catch (\Throwable $e) {
            // Never break the app if settings are unavailable (e.g. during migrate).
        }
    }

    /** Decrypt the stored SMTP password (stored encrypted). */
    private static function password(): ?string
    {
        $stored = Setting::get('mail_password');
        if (! $stored) {
            return null;
        }
        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
