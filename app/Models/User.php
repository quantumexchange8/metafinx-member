<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia, MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'address_1',
        'address_2',
        'verification_type',
        'kyc_approval',
        'kyc_approval_description',
        'referral_code',
        'upline_id',
        'hierarchyList',
        'status',
        'setting_rank_id',
        'total_affiliate',
        'self_deposit',
        'valid_affiliate_deposit',
        'identity_number',
        'role',
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
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function wallets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wallet::class, 'user_id', 'id' );
    }

    public function setReferralId(): void
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVXYZabcdefghijklmnopqrstuvwxyz';
        $idLength = strlen((string)$this->id);

        $temp_code = substr(str_shuffle($characters), 0, 8 - $idLength);
        $alphabetId = '';

        foreach (str_split((string)$this->id) as $digit) {
            $alphabetId .= $characters[$digit];
        }

        $this->referral_code = $temp_code . $alphabetId;
        $this->save();
    }

    public function getChildrenIds(): array
    {
        return User::query()->where('hierarchyList', 'like', '%-' . $this->id . '-%')
            ->where('status', 1)
            ->pluck('id')->toArray();
    }

    public function upline(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'upline_id', 'id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'upline_id', 'id');
    }

    public function getDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        $this->loadDescendants($this, $descendants);

        return $descendants;
    }

    public function getLevel(): int
    {
        $level = 1;
        $parent = $this;

        while ($parent->upline) {
            $level++;
            $parent = $parent->upline;
        }

        return $level;
    }

    protected function loadDescendants($user, &$descendants): void
    {
        foreach ($user->children as $descendant) {
            $descendants->push($descendant);
            $this->loadDescendants($descendant, $descendants);
        }
    }

    public function earnings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Earning::class, 'upline_id', 'id');
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvestmentSubscription::class, 'user_id', 'id');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }
}
