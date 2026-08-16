<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Detalles - {{ $resultado->nombre_estudiante }}</title>
    <style>
        @page {
            margin: 28px 36px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 10px;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .header td {
            vertical-align: middle;
        }

        .header .logo-cell {
            width: 55px;
        }

        .header .logo-cell img {
            width: 45px;
            height: 45px;
        }

        .header .institution {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #6b7280;
            text-transform: uppercase;
            margin: 0 0 2px 0;
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
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
        }

        table.respuestas {
            width: 100%;
            border-collapse: collapse;
        }

        table.respuestas th,
        table.respuestas td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            text-align: left;
            font-size: 9px;
        }

        table.respuestas th {
            background-color: #f3f4f6;
            text-transform: uppercase;
            color: #6b7280;
            font-size: 8.5px;
        }

        table.respuestas td.center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
        }

        .badge-inatencion {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-hiperactividad {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .score-badge {
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 9px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .score-badge-0 {
            background-color: #d1fae5;
            color: #047857;
        }

        .score-badge-1 {
            background-color: #fef9c3;
            color: #a16207;
        }

        .score-badge-2 {
            background-color: #fed7aa;
            color: #c2410c;
        }

        .score-badge-3 {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('/images/logo/logo-xl.png') }}" />
            </td>
            <td>
                <h1>Detalles de respuestas</h1>
                <p class="subtitle" style="margin: 0;">{{ $resultado->encuesta->nombre }}</p>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Datos del estudiante</h2>
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

    @if ($analisis)
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
                        <td>{{ $analisis->puntuacion_inatencion }}/27</td>
                        <td>{{ $analisis->porcentaje_inatencion }}%</td>
                        <td>{{ $analisis->sintomas_inatencion }}/9</td>
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
    @endif

    <div class="section">
        <h2>Respuestas ({{ count($respuestas) }})</h2>
        <table class="respuestas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pregunta</th>
                    <th>Categoría</th>
                    <th>Respuesta</th>
                    <th class="center">Puntuación</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $responseLabels = [
                        0 => 'Nunca o raramente',
                        1 => 'A veces',
                        2 => 'Con frecuencia',
                        3 => 'Muy frecuentemente',
                    ];
                @endphp
                @foreach ($respuestas as $index => $respuesta)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $respuesta->pregunta->nombre }}<br>
                            <span style="color: #6b7280;">{{ $respuesta->pregunta->descripcion }}</span>
                        </td>
                        <td>
                            @if (($respuesta->pregunta->tipo_tda ?? 'I') === 'H')
                                <span class="badge badge-hiperactividad">Hiperactividad</span>
                            @else
                                <span class="badge badge-inatencion">Inatención</span>
                            @endif
                        </td>
                        <td>{{ $responseLabels[$respuesta->puntuacion] ?? '-' }}</td>
                        <td class="center">
                            <span class="score-badge score-badge-{{ $respuesta->puntuacion }}">{{ $respuesta->puntuacion }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
