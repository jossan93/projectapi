<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ChatHistory extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'session_id',
        'user_message',
        'bot_response',
    ];
}
