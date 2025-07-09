<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsModel extends Model
{
    use HasFactory;


    protected $table = "events";

    protected $fillable = [
        "name",
        "description",
        "date",
        "link",
        "is_active",
    ];


    public function user_events()
    {
        return $this->hasMany(UsersEventsModel::class, "event_id", "id");
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "user_events", "event_id", "user_id");
    }
}
