<?php

namespace App\Support;

/**
 * The admin features that can be granted per role — one permission each.
 * Key = permission name; value = human label shown in the UI.
 */
class Features
{
    public const LIST = [
        'applications' => 'Visa Applications',
        'leads' => 'Leads & Inquiries',
        'clients' => 'Clients',
        'invoices' => 'Invoices',
        'tickets' => 'Flight Tickets',
        'finance' => 'Finance',
        'visas' => 'Visa Destinations',
        'packages' => 'Tour Packages',
        'settings' => 'Settings',
        'staff' => 'Staff & Roles',
    ];

    /** Default permissions granted to the built-in "agent" role. */
    public const AGENT_DEFAULTS = ['applications', 'leads', 'clients', 'invoices', 'tickets'];

    public static function keys(): array
    {
        return array_keys(self::LIST);
    }

    public static function options(): array
    {
        return collect(self::LIST)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values()->all();
    }
}
