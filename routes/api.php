<?php

// Las rutas que antes vivían aquí (encuestas/respuestas usadas por el flujo
// de encuesta) se movieron a routes/web.php: ya usaban el grupo de middleware
// 'web' (sesión + CSRF) en vez del stateless por defecto de este archivo.
// Este archivo queda disponible para una API stateless real si se necesita.
