<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'address', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function flightTickets(): HasMany
    {
        return $this->hasMany(FlightTicket::class);
    }

    /** Is this user a staff member (has any role) rather than a client? */
    public function isStaff(): bool
    {
        return $this->roles()->exists();
    }

    /** Scope to clients only — users with no role at all. */
    public function scopeClients(Builder $query): Builder
    {
        return $query->whereDoesntHave('roles');
    }

    /** Scope to staff — users with at least one role. */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->whereHas('roles');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
