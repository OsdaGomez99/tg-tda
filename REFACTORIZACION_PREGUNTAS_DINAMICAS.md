# Refactorización: Preguntas dinámicas desde BD vs Hardcoded

## 🔄 Cambios Realizados

### Problema Original

- Las 18 preguntas estaban **hardcodeadas** en `TdaAnalysisService::getQuestions()`
- El formulario de creación de preguntas creaba registros en la BD pero no eran usados
- Existía conflicto entre `categoria_id` y `tipo_tda` (redundancia)

### Solución Implementada

#### 1. **TdaAnalysisService.php** - Refactorización

```php
// ANTES: Las 18 preguntas hardcodeadas en getQuestions()
public function getQuestions(): array {
    return [ /* 18 preguntas... */ ];
}

// DESPUÉS: Obtiene preguntas de la BD
public function getQuestions(): array {
    $preguntas = Pregunta::where('estado', true)
        ->whereNotNull('tipo_tda')
        ->orderBy('id')
        ->get();

    return $preguntas->map(fn($p) => [
        'id'       => $p->id,
        'category' => $p->tipo_tda,
        'text'     => $p->nombre,
        'example'  => $p->ejemplo,
    ])->toArray();
}

// NUEVO: Backup de preguntas DSM-5 por si BD está vacía
public function getDefaultQuestions(): array {
    return [ /* 18 preguntas DSM-5 por defecto... */ ];
}

// NUEVO: Obtiene preguntas con fallback a default
public function getAvailableQuestions(): array {
    $questions = $this->getQuestions();
    return count($questions) > 0 ? $questions : $this->getDefaultQuestions();
}
```

#### 2. **Migración de Preguntas** - Actualizada

```php
// ANTES: categoria_id requerido
$table->foreignId('categoria_id')->constrained('categorias');
$table->char('tipo_tda', 1)->nullable();

// DESPUÉS: categoria_id opcional, tipo_tda y ejemplo requeridos
$table->foreignId('categoria_id')->constrained('categorias')->nullable();
$table->char('tipo_tda', 1)->nullable();
$table->text('ejemplo')->nullable();
```

#### 3. **Controladores** - Actualizados

- `ApiEncuestaController`: Cambia `getQuestions()` → `getAvailableQuestions()`
- `EncuestaWebController`: Pasa preguntas desde servicio
- `Encuesta.php`: Método estático usa `getAvailableQuestions()`

#### 4. **Vistas** - Limpias

- `encuestas-detalles.blade.php`: Eliminada línea que intentaba concatenar una función
- Las preguntas ahora vienen con las respuestas (relación `pregunta` cargada)

## 📊 Flujo Ahora

```
Usuario crea preguntas en formulario
    ↓
Se guardan en BD (Pregunta model)
    ↓
Encuesta obtiene preguntas de BD
    ↓
getAvailableQuestions() retorna desde BD
    ↓
Cuestionario muestra preguntas dinámicas
    ↓
Respuestas se analizan correctamente
```

## ✅ Validaciones

- Solo preguntas con `estado = true` y `tipo_tda NOT NULL` se usan
- Si BD está vacía, usa preguntas por defecto (DSM-5)
- Todos los IDs deben coincidir entre BD y análisis
- El campo `ejemplo` es opcional (puede venir null)

## 🗄️ Esquema de Preguntas

```
id          | INTEGER | PK
nombre      | STRING  | Texto de la pregunta
descripcion | TEXT    | Descripción adicional (opcional)
tipo_tda    | CHAR(1) | 'I' (Inatención) o 'H' (Hiperactividad)
ejemplo     | TEXT    | Ejemplo de comportamiento (opcional)
estado      | BOOLEAN | Si está activa (true) o no (false)
categoria_id| FK      | Categoría (deprecated pero mantenido)
created_at  | TIMESTAMP
updated_at  | TIMESTAMP
```

## 🚀 Próximos Pasos

1. Ejecutar migraciones: `php artisan migrate`
2. Ejecutar seeder: `php artisan db:seed --class=PreguntasSeeder`
3. Verificar que 18 preguntas tengan `tipo_tda` definido
4. Crear encuesta y responder cuestionario
5. Confirmar que preguntas vienen de BD, no hardcoded
