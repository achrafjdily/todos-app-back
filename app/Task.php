<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Task extends Model
{
    //
    protected $fillable = [
        'name', 'date', 'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
