<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequestModel extends Model
{
    use HasFactory;
    protected $table = 'user_requests';
    protected $fillable = ['date', 'department', 'name', 'researchRequirement', 'scope', 'status', 'user_id', 'adminResponse'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
