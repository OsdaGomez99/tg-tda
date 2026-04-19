<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'categoria_id', 'estado'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
