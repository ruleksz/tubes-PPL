<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CurhatMessage extends Model
{
    protected $fillable = ['curhat_id','sender','message'];

    public function curhat()
    {
        return $this->belongsTo(Curhat::class);
    }
}