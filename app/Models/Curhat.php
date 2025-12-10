<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Curhat extends Model
{
    protected $fillable = ['title','message','anonymous','category','status'];

    public function messages()
    {
        return $this->hasMany(CurhatMessage::class);
    }
}
