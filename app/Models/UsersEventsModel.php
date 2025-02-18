<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersEventsModel extends Model
{
    use HasFactory;

    protected $table = 'user_events';

    protected $fillable = [
        'user_id',
        'event_id',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function events()
    {
        return $this->belongsTo(EventsModel::class, 'event_id', 'id');
    }




}
