<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Resultado - {{ $resultado->nombre_estudiante }}</title>
    <style>
        @page {
            margin: 28px 36px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #111827;
        }

        h2 {
            font-size: 13px;
            margin: 0 0 8px 0;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }

        .subtitle {
            color: #6b7280;
            margin: 0 0 16px 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            color: #ffffff;
            background-color: {{ $badgeColor }};
        }

        .section {
            margin-bottom: 16px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        table.info td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.info td.label {
            width: 140px;
            color: #6b7280;
        }

        table.scores {
            width: 100%;
            border-collapse: collapse;
        }

        table.scores th,
        table.scores td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }

        table.scores th {
            background-color: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px 12px;
        }

        .box-note {
            background-color: #ecfdf5;
            border: 1px solid #6ee7b7;
            border-radius: 4px;
            padding: 10px 12px;
            color: #065f46;
        }

        ul {
            margin: 6px 0 0 0;
            padding-left: 16px;
        }

        li {
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <h1>Resultado de Evaluación TDA</h1>
    <p class="subtitle">{{ $resultado->encuesta->nombre }}</p>

    <div class="section">
        <h2>Datos del Estudiante</h2>
        <table class="info">
            <tr>
                <td class="label">Nombre:</td>
                <td>{{ $resultado->nombre_estudiante }}</td>
                <td class="label">Edad:</td>
                <td>{{ $resultado->edad_estudiante }} años</td>
            </tr>
            <tr>
                <td class="label">Documento:</td>
                <td>{{ $resultado->documento_estudiante }}</td>
                <td class="label">Sexo:</td>
                <td>{{ ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'][$resultado->sexo_estudiante] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Carrera:</td>
                <td>{{ $resultado->carrera->nombre ?? 'No especificada' }}</td>
                <td class="label">Semestre:</td>
                <td>{{ $resultado->semestre->nombre ?? 'No especificado' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Resultado Principal</h2>
        <p><span class="badge">{{ $resultadoLabel }}</span></p>
    </div>

    <div class="section">
        <h2>Puntuaciones</h2>
        <table class="scores">
            <thead>
                <tr>
                    <th>Dimensión</th>
                    <th>Puntuación</th>
                    <th>Porcentaje</th>
                    <th>Síntomas significativos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Inatención</td>
                    <td>{{ $analisis->puntuacion_inatención }}/27</td>
                    <td>{{ $analisis->porcentaje_inatención }}%</td>
                    <td>{{ $analisis->sintomas_inatención }}/9</td>
                </tr>
                <tr>
                    <td>Hiperactividad/Impulsividad</td>
                    <td>{{ $analisis->puntuacion_hiperactividad }}/27</td>
                    <td>{{ $analisis->porcentaje_hiperactividad }}%</td>
                    <td>{{ $analisis->sintomas_hiperactividad }}/9</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>{{ $analisis->puntuacion_total }}/54</strong></td>
                    <td><strong>{{ round(($analisis->puntuacion_total / 54) * 100) }}%</strong></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Análisis Detallado</h2>
        <div class="box">
            <p><strong>Resultado:</strong> {{ $analisis->getResultadoDescripcion() }}</p>
            <p>{{ $analisis->descripcion }}</p>
        </div>
    </div>

    @if (count($recomendaciones) > 0)
        <div class="section">
            <h2>Orientación y Pautas de Organización Académica</h2>
            <div class="box-note">
                <ul>
                    @foreach ($recomendaciones as $recomendacion)
                        <li>{{ $recomendacion }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>
            Este reporte ha sido generado con fines de cribado y orientación psicoeducativa. No constituye un
            diagnóstico definitivo. El personal del Área de Bienestar Estudiantil tiene acceso confidencial a estos
            resultados para ofrecer estrategias de apoyo personalizadas.
        </p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>

</html>
