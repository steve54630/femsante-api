<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'QUESTIONS';
    protected $primaryKey = 'QUEST_ID';
    public $timestamps = false; // pas de created_at / updated_at

    protected $fillable = [
        'QUEST_STRING',
    ];

    // Relation avec User
    public function users()
    {
        return $this->hasMany(User::class, 'QUEST_ID', 'QUEST_ID');
    }
}