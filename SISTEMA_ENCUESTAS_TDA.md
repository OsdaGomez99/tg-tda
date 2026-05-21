# Sistema de Encuestas de TDA - Documentación de Uso

## Descripción General

Sistema completo de encuestas y análisis para detectar Trastorno por Déficit de Atención (TDA) basado en criterios DSM-5. Incluye 18 preguntas clínicas divididas en dos categorías: Inatención (9) e Hiperactividad/Impulsividad (9).

## Características

- **18 Preguntas DSM-5**: Cuestionario validado para evaluación de TDA
- **Escala Likert 0-3**: Nunca o raramente (0), A veces (1), Con frecuencia (2), Muy frecuentemente (3)
- **Análisis Automático**: Evaluación según criterios clínicos
- **5 Resultados Posibles**:
    - `tda_combinado`: ≥6 síntomas en ambas categorías
    - `tda_inatento`: ≥6 síntomas en inatención
    - `tda_hiperactivo`: ≥6 síntomas en hiperactividad
    - `tda_possible`: Síntomas moderados (3-5)
    - `no_tda`: Síntomas leves

## Endpoints API

### 1. Obtener Encuestas Disponibles

```
GET /api/encuestas
```

Retorna lista de todas las encuestas disponibles.

### 2. Obtener Encuesta Específica

```
GET /api/encuestas/{encuesta_id}
```

Retorna encuesta con sus 18 preguntas y opciones de respuesta.

### 3. Iniciar Encuesta

```
POST /api/encuestas/{encuesta_id}/iniciar
Content-Type: application/json

{
  "nombre_estudiante": "Juan Pérez",
  "edad_estudiante": 25,
  "sexo_estudiante": "M"
}
```

**Respuesta:**

- `resultado_id`: ID del registro de respuesta para guardar las respuestas
- `preguntas`: Array con las 18 preguntas
- `opciones_respuesta`: Mapeo de puntuaciones (0-3)

### 4. Guardar Respuesta Individual

```
POST /api/respuestas/{resultado_id}
Content-Type: application/json

{
  "pregunta_id": 1,
  "puntuacion": 2
}
```

Guarda la respuesta a una pregunta específica. Retorna progreso.

### 5. Guardar Múltiples Respuestas

```
POST /api/respuestas/{resultado_id}/batch
Content-Type: application/json

{
  "respuestas": [
    {"pregunta_id": 1, "puntuacion": 2},
    {"pregunta_id": 2, "puntuacion": 1},
    {"pregunta_id": 3, "puntuacion": 3}
  ]
}
```

Guarda múltiples respuestas de una vez (más eficiente).

### 6. Finalizar Encuesta y Generar Análisis

```
POST /api/respuestas/{resultado_id}/finalizar
```

Valida que todas las 18 preguntas estén respondidas, realiza el análisis DSM-5 y genera el reporte.

**Respuesta:**

```json
{
    "success": true,
    "data": {
        "resultado": {
            "id": 1,
            "nombre_estudiante": "Juan Pérez",
            "edad_estudiante": 25,
            "sexo_estudiante": "M"
        },
        "analisis": {
            "resultado": "tda_inatento",
            "puntuacion_inatención": 20,
            "puntuacion_hiperactividad": 8,
            "puntuacion_total": 28,
            "sintomas_inatención": 8,
            "sintomas_hiperactividad": 2,
            "porcentaje_inatención": 74.07,
            "porcentaje_hiperactividad": 29.63,
            "descripcion": "Resultado detallado...",
            "descripcion_resultado": "TDA Tipo Inatento..."
        },
        "respuestas_count": 18
    }
}
```

### 7. Obtener Resultado de Encuesta

```
GET /api/respuestas/{resultado_id}
```

Obtiene el análisis completado de una encuesta.

### 8. Obtener Todos los Resultados de una Encuesta

```
GET /api/encuestas/{encuesta_id}/resultados
```

Retorna todos los resultados y análisis de una encuesta específica.

### 9. Obtener Estadísticas

```
GET /api/encuestas/{encuesta_id}/estadisticas
```

**Respuesta:**

```json
{
    "estadisticas": {
        "total_respondientes": 25,
        "resultados_completados": 25,
        "distribucion_resultados": {
            "tda_combinado": 5,
            "tda_inatento": 8,
            "tda_hiperactivo": 3,
            "tda_possible": 4,
            "no_tda": 5
        },
        "promedio_inatención": 18.5,
        "promedio_hiperactividad": 12.3,
        "promedio_total": 30.8,
        "edad_promedio": 23.4,
        "distribucion_genero": {
            "M": 12,
            "F": 13,
            "O": 0
        }
    }
}
```

## Flujo de Uso Típico

1. **Obtener encuesta disponible**

    ```bash
    curl http://localhost:8000/api/encuestas
    ```

2. **Iniciar respuesta**

    ```bash
    curl -X POST http://localhost:8000/api/encuestas/1/iniciar \
      -H "Content-Type: application/json" \
      -d '{
        "nombre_estudiante": "Juan Pérez",
        "edad_estudiante": 25,
        "sexo_estudiante": "M"
      }'
    ```

    Guardar el `resultado_id` de la respuesta.

3. **Responder preguntas (opción A: una por una)**

    ```bash
    curl -X POST http://localhost:8000/api/respuestas/1 \
      -H "Content-Type: application/json" \
      -d '{"pregunta_id": 1, "puntuacion": 2}'
    ```

    O **(opción B: todas de una vez)**

    ```bash
    curl -X POST http://localhost:8000/api/respuestas/1/batch \
      -H "Content-Type: application/json" \
      -d '{
        "respuestas": [
          {"pregunta_id": 1, "puntuacion": 2},
          {"pregunta_id": 2, "puntuacion": 1},
          ...
          {"pregunta_id": 18, "puntuacion": 3}
        ]
      }'
    ```

4. **Finalizar y obtener análisis**

    ```bash
    curl -X POST http://localhost:8000/api/respuestas/1/finalizar
    ```

5. **Ver resultados consolidados**
    ```bash
    curl http://localhost:8000/api/respuestas/1
    ```

## Base de Datos

### Tablas Nuevas

**respuestas_encuestas**

- id: bigint
- encuesta_resultado_id: foreignId
- pregunta_id: foreignId
- puntuacion: integer (0-3)
- unique(encuesta_resultado_id, pregunta_id)

**analisis_tda**

- id: bigint
- encuesta_resultado_id: foreignId (unique)
- puntuacion_inatención: integer
- puntuacion_hiperactividad: integer
- puntuacion_total: integer
- sintomas_inatención: integer
- sintomas_hiperactividad: integer
- resultado: enum (tda_combinado, tda_inatento, tda_hiperactivo, tda_possible, no_tda)
- porcentaje_inatención: decimal(5,2)
- porcentaje_hiperactividad: decimal(5,2)
- descripcion: text

**Campos Nuevos en preguntas**

- tipo_tda: char(1) - 'I' o 'H'
- ejemplo: text

## Instalación y Configuración

1. **Ejecutar migraciones**

    ```bash
    php artisan migrate
    ```

2. **Ejecutar seeders**

    ```bash
    php artisan db:seed --class=CategoriasSeeder
    php artisan db:seed --class=PreguntasSeeder
    ```

3. **Crear encuesta de prueba** (vía Laravel Tinker o controlador)
    ```php
    Encuesta::create([
        'nombre' => 'Screening TDA 2026',
        'usuario_id' => 1
    ]);
    ```

## Criterios DSM-5 Implementados

- **Inatención**: 9 síntomas, umbral ≥6 síntomas con puntuación ≥2
- **Hiperactividad/Impulsividad**: 9 síntomas, umbral ≥6 síntomas con puntuación ≥2
- **Síntomas Posibles**: 3-5 síntomas en cualquier categoría
- **No TDA**: Menos de 3 síntomas significativos

## Modelos Disponibles

- `App\Models\Encuesta`: Encuestas disponibles
- `App\Models\EncuestaResultado`: Respuestas de estudiantes
- `App\Models\RespuestaEncuesta`: Respuestas individuales por pregunta
- `App\Models\AnalisisTda`: Análisis y resultados
- `App\Models\Pregunta`: Preguntas del cuestionario
- `App\Models\Categoria`: Categorías de preguntas

## Servicio Principal

`App\Services\TdaAnalysisService`

Métodos principales:

- `getQuestions()`: Obtiene las 18 preguntas
- `getResponseOptions()`: Obtiene opciones de respuesta (0-3)
- `analyze(array $answers)`: Realiza análisis
- `guardarRespuestasYAnalizar()`: Guarda respuestas y genera análisis
- `obtenerResultado()`: Obtiene resultado completo
- `exportarResultado()`: Exporta en formato API
- `estadisticas()`: Genera estadísticas agregadas

## Notas Técnicas

- Todas las respuestas son persistentes en BD
- El análisis se realiza automáticamente al finalizar
- Se mantiene historial completo de respuestas
- Soporta múltiples respondientes por encuesta
- APIs RESTful completamente documentadas
