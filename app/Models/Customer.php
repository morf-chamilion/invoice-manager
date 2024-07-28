<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Traits\HasCreatedBy;
use App\Models\Traits\HasUpdatedBy;
use App\Notifications\Customer\CustomerVerifyNotification;
use App\Notifications\Customer\CustomerResetPasswordNotification;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;
use Illuminate\Auth\MustVerifyEmail as AuthMustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class Customer extends Model implements Authenticatable, AuthCanResetPassword, MustVerifyEmail, HasRelationsInterface
{
    use HasFactory;
    use Notifiable;
    use HasCreatedBy;
    use HasUpdatedBy;
    use CanResetPassword;
    use AuthAuthenticatable;
    use AuthMustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'name',
        'email',
        'phone',
        'address',
        'company',
        'password',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => CustomerStatus::class,
    ];

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', CustomerStatus::ACTIVE);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomerVerifyNotification($this));
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'status' => CustomerStatus::ACTIVE,
        ])->save();
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    public function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            AuthRoutePath::VERIFICATION_VERIFY,
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->email)
            ],
        );
    }

    /**
     * Define model methods with Has relations.
     */
    public function defineHasRelationships(): array
    {
        return [];
    }

    /**
     * Get the invoices associated with the customer.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
