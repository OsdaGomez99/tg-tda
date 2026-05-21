# Encuestas Personalizables: Asignación de Preguntas

## 🎯 Objetivo

Cada encuesta puede tener su **propio conjunto de preguntas**, en lugar de usar todas las preguntas del sistema.

## 🏗️ Nueva Arquitectura

### Tabla: `encuesta_pregunta` (Relación Muchos-a-Muchos)

```sql
id              | INTEGER | PK
encuesta_id     | FK      | Referencia a encuestas
pregunta_id     | FK      | Referencia a preguntas
orden           | INTEGER | Orden de aparición (1, 2, 3...)
created_at      | TIMESTAMP
updated_at      | TIMESTAMP

-- Constraints
UNIQUE (encuesta_id, pregunta_id)  -- Una pregunta solo 1 vez por encuesta
INDEX (encuesta_id, orden)         -- Para ordenamiento
```

## 📊 Ejemplo de Flujo

### Antes (Problema)

```
Encuesta A ──┐
             ├─→ Todas las 18 preguntas del sistema
Encuesta B ──┘   (redundancia, no hay personalización)
```

### Ahora (Solución)

```
Encuesta A ──→ Preguntas 1, 3, 5, 7, 9, 11, 13, 15, 17 (Inatención)
Encuesta B ──→ Preguntas 2, 4, 6, 8, 10, 12, 14, 16, 18 (Hiperactividad)
Encuesta C ──→ Preguntas 1, 2, 4, 6, 8, 10 (Combinado específico)
```

## 🔄 Proceso de Uso

### 1. Admin Crea Encuesta

```
GET /admin/encuestas/crear
POST /admin/encuestas
Nombre: "Screening TDA Primaria"
```

### 2. Admin Asigna Preguntas

```
GET /admin/encuestas/{id}/asignar-preguntas

# Selecciona qué preguntas
[✓] Pregunta 1 - Inatención
[✓] Pregunta 2 - Hiperactividad
[ ] Pregunta 3 - Inatención
...

POST /admin/encuestas/{id}/asignar-preguntas
pregunta_ids: [1, 2, 4, 5, 7]
```

### 3. Estudiante Responde

```
GET /encuestas/{id}/iniciar
POST /api/encuestas/{id}/iniciar
→ Retorna SOLO las preguntas asignadas a esa encuesta

POST /api/respuestas/{resultado}
→ Valida que pregunta_id ∈ preguntas de encuesta
```

### 4. Análisis

```
POST /api/respuestas/{resultado}/finalizar
→ Valida: respuestas.count == preguntas_asignadas.count
→ Genera análisis solo con esas preguntas
```

## 🔗 Relaciones en Modelos

### Modelo Encuesta

```php
public function preguntas(): BelongsToMany
{
    return $this->belongsToMany(Pregunta::class, 'encuesta_pregunta')
        ->withPivot('orden')
        ->orderBy('encuesta_pregunta.orden');
}

public function getPreguntasDisponibles(): array
{
    // Retorna preguntas asignadas formateadas
    // O fallback a todas las activas si está vacía
}
```

## 📋 Cambios en Controladores

### ApiEncuestaController

- `show()`: `encuesta->getPreguntasDisponibles()`
- `iniciar()`: Obtiene preguntas específicas
- `guardarRespuesta()`: Valida `pregunta_id ∈ encuesta.preguntas`
- `guardarRespuestas()`: Valida todas
- `finalizar()`: Valida count de respuestas vs encuesta

### EncuestaController (Admin)

- `asignarPreguntasForm()`: Muestra checkboxes de preguntas
- `asignarPreguntas()`: Guarda con `encuesta->preguntas()->sync($sync)`

## 🚀 Pasos Siguientes

1. Ejecutar migración:

    ```bash
    php artisan migrate
    ```

2. Crear vistas:
    - `encuestas-create.blade.php` - Crear encuesta
    - `encuestas-asignar-preguntas.blade.php` - Asignar preguntas
    - `encuestas-edit.blade.php` - Editar encuesta

3. Popular datos:

    ```bash
    php artisan db:seed --class=PreguntasSeeder
    ```

4. Crear una encuesta de prueba desde admin:
    - Crear encuesta "Test TDA"
    - Asignar 9 preguntas de Inatención
    - Probar responder desde estudiante

## ✅ Validaciones Automáticas

```php
// Si estudiante intenta responder pregunta no asignada
if (!$preguntasEncuesta->contains($preguntaId)) {
    return 400: "La pregunta no pertenece a esta encuesta"
}

// Si intenta finalizar sin responder todas
if ($respuestas.count() < $totalPreguntas) {
    return 400: "Faltan respuestas"
}
```

## 📚 Ejemplo SQL

```sql
-- Encuesta "Test Inatención"
INSERT INTO encuestas (nombre, usuario_id) VALUES ('Test Inatención', 1);

-- Asignar preguntas 1-9 (Inatención)
INSERT INTO encuesta_pregunta (encuesta_id, pregunta_id, orden)
VALUES
  (1, 1, 1),
  (1, 2, 2),
  (1, 3, 3),
  ...
  (1, 9, 9);
```

## 🎯 Beneficios

✅ Encuestas personalizadas por contexto/grupo
✅ Reducir tiempo de respuesta (menos preguntas si es necesario)
✅ Facilitar comparación entre grupos específicos
✅ Soporte para estudios parciales
✅ Escalable a múltiples baterías de evaluación
