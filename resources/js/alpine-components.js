/**
 * Registro global de componentes Alpine.js
 * Importa los módulos de formularios y los registra con Alpine.data()
 */

import { loginForm } from './forms/login-form.js';
import { registerForm } from './forms/register-form.js';

// Registrar componentes globalmente
Alpine.data('loginForm', loginForm);
Alpine.data('registerForm', registerForm);
