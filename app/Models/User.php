<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Enums\UserRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'oauth_id',
        'name',
        'slug',
        'email',
        'password',
        'last_login_at'
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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    protected static function boot()
    {
        static::creating(function (User $user) {
            $sameName = User::where('name', 'like', "%{$user->name}%")->count();
            $slug = Str::slug($user->name);

            if (!$sameName) {
                $user->slug = $slug;
                return;
            }

            $user->slug = $slug . '-' . ($sameName + 1);
        });

        parent::boot();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeWhereHasRoles(Builder $builder): Builder
    {
        return $builder->whereHas('roles');
    }

    public function scopeWhereDoesntHaveRoles(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('roles');
    }

    public function isOauthUser()
    {
        return $this->oauth_id;
    }

    public function scopeFilter(Builder $builder, $term): Builder
    {
        return $builder->when($term, function (Builder $builder) use ($term) {
            $builder->where(function (Builder $builder) use ($term) {
                return $builder->where('name', 'LIKE', "%$term%")
                    ->orWhere('email', 'LIKE', "%$term%");
            });
        });
    }
}
