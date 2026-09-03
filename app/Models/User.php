<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $table = 'USERS';
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
        'LIFETIME_PURCHASED',
        'FREE_TRIAL_USED_AT',
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
            'LIFETIME_PURCHASED' => 'boolean',
            'FREE_TRIAL_USED_AT' => 'datetime',
        ];
    }

    /**
    * Relation : un user a une question
    */

    public function question() {
        return $this->belongsTo( Question::class, 'QUEST_ID', 'QUEST_ID' );
    }

    /**
     * Accès premium actuel : vrai achat "à vie", ou abonnement (payant ou essai gratuit)
     * dont la date de validité n'est pas dépassée. VALID_DATE = null ne suffit plus, à lui
     * seul, à signifier un accès à vie (ambigu avec un compte qui n'a jamais payé).
     */
    public function hasAccess(): bool {
        if ($this->LIFETIME_PURCHASED) {
            return true;
        }

        return $this->VALID_DATE !== null && Carbon::parse($this->VALID_DATE)->gte(Carbon::today());
    }
}
