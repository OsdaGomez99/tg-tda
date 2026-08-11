<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Semestre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function encuestaResultados(): HasMany
    {
        return $this->hasMany(EncuestaResultado::class);
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Devuelve el semestre actualmente activo, o null si no hay ninguno.
     */
    public static function actual(): ?self
    {
        return static::activo()->first();
    }

    /**
     * Marca este semestre como el único activo, desactivando todos los demás.
     */
    public function activar(): void
    {
        DB::transaction(function () {
            static::query()->where('id', '!=', $this->id)->update(['activo' => false]);
            $this->update(['activo' => true]);
        });
    }
}
