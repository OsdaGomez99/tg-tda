<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Estadísticas - {{ $encuesta->nombre }}</title>
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

        table.summary {
            width: 100%;
            border-collapse: collapse;
        }

        table.summary th,
        table.summary td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }

        table.summary th {
            background-color: #f3f4f6;
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
        }

        table.respondientes {
            width: 100%;
            border-collapse: collapse;
        }

        table.respondientes th,
        table.respondientes td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            text-align: left;
            font-size: 9.5px;
        }

        table.respondientes th {
            background-color: #f3f4f6;
            text-transform: uppercase;
            color: #6b7280;
            font-size: 8.5px;
        }

        .stat-box {
            display: inline-block;
            width: 18%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            margin-right: 1%;
            text-align: center;
        }

        .stat-box .value {
            font-size: 16px;
            font-weight: bold;
        }

        .stat-box .label {
            color: #6b7280;
            font-size: 9px;
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
    <h1>Estadísticas de Encuesta</h1>
    <p class="subtitle">{{ $encuesta->nombre }} — {{ $estadisticas['total_respondientes'] ?? 0 }} respondientes</p>

    @if ($estadisticas && count($estadisticas) > 0)
        <div class="section">
            <h2>Distribución de Resultados</h2>
            <table class="summary">
                <thead>
                    <tr>
                        <th>TDA Combinado</th>
                        <th>TDA Inatento</th>
                        <th>TDA Hiperactivo</th>
                        <th>Posible TDA</th>
                        <th>Sin TDA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach (['tda_combinado', 'tda_inatento', 'tda_hiperactivo', 'tda_posible', 'no_tda'] as $tipo)
                            <td>
                                {{ $estadisticas['distribucion_resultados'][$tipo] ?? 0 }}
                                ({{ round((($estadisticas['distribucion_resultados'][$tipo] ?? 0) / ($estadisticas['total_respondientes'] ?: 1)) * 100) }}%)
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Puntuaciones Promedio</h2>
            <table class="summary">
                <thead>
                    <tr>
                        <th>Inatención (de 27)</th>
                        <th>Hiperactividad (de 27)</th>
                        <th>Total (de 54)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $estadisticas['promedio_inatención'] ?? 0 }}</td>
                        <td>{{ $estadisticas['promedio_hiperactividad'] ?? 0 }}</td>
                        <td>{{ $estadisticas['promedio_total'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Datos Demográficos</h2>
            <table class="summary">
                <thead>
                    <tr>
                        <th>Edad Promedio</th>
                        <th>Masculino</th>
                        <th>Femenino</th>
                        <th>Otro</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $estadisticas['edad_promedio'] ?? 0 }} años</td>
                        <td>{{ $estadisticas['distribucion_genero']['M'] ?? 0 }}</td>
                        <td>{{ $estadisticas['distribucion_genero']['F'] ?? 0 }}</td>
                        <td>{{ $estadisticas['distribucion_genero']['O'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Listado de Respondientes</h2>
            <table class="respondientes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>Género</th>
                        <th>Resultado</th>
                        <th>Puntuación Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resultados as $resultado)
                        <tr>
                            <td>{{ $resultado->nombre_estudiante }}</td>
                            <td>{{ $resultado->edad_estudiante }}</td>
                            <td>{{ ['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'][$resultado->sexo_estudiante] ?? '-' }}</td>
                            <td>
                                {{ $resultado->analisisTda
                                    ? match ($resultado->analisisTda->resultado) {
                                        'tda_combinado' => 'TDA Combinado',
                                        'tda_inatento' => 'TDA Inatento',
                                        'tda_hiperactivo' => 'TDA Hiperactivo',
                                        'tda_posible' => 'Posible TDA',
                                        'no_tda' => 'Sin TDA',
                                        default => 'Desconocido',
                                    }
                                    : 'Pendiente' }}
                            </td>
                            <td>{{ $resultado->analisisTda ? $resultado->analisisTda->puntuacion_total . '/54' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No hay respondientes aún</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <p>No hay resultados disponibles para esta encuesta aún.</p>
    @endif

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>

</html>
