<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'ID';

    /**
    * The attributes that are mass assignable.
    *
    * @var list<string>
    */
    protected $fillable = [
        'NAME',
        'EMAIL',
        'PASSWORD',
        'VALID_DATE',
        'QUEST_ANSWER',
        'QUEST_ID',
    ];

    /**
    * The attributes that should be hidden for serialization.
    *
    * @var list<string>
    */
    protected $hidden = [
        'PASSWORD',
    ];

    /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array {
        return [
            'VALID_DATE' => 'date',
            'PASSWORD' => 'hashed',
        ];
    }

    /**
    * Le mot de passe est stocke dans la colonne PASSWORD (non standard).
    * On l'indique a Laravel pour l'authentification.
    */
    public function getAuthPassword() {
        return $this->PASSWORD;
    }

    /**
    * Relation : un user a une question
    */

    public function question() {
        return $this->belongsTo( Question::class, 'QUEST_ID', 'QUEST_ID' );
    }
}
