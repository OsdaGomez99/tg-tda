<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $fillable = ['nombre', 'usuario_id'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
