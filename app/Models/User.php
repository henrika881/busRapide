<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'id',
        'nom',
        'prenom',
        'telephone',
        'email',
        'password',
        'date_creation',
        'numero_cni',
        'type_utilisateur',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get active sessions count for the user
     */
    public function getActiveSessionsCount()
    {
        // Si la table sessions existe et le driver est database
        if (config('session.driver') === 'database') {
            return \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $this->id)
                ->count();
        }
        
        return 1; // Fallback
    }
}
