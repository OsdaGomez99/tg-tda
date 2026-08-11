import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Importar componentes
import { loginForm } from './forms/login-form.js';
import { registerForm } from './forms/register-form.js';

// Registrar con Alpine.data() ANTES de Alpine.start()
// Alpine.data() espera la FUNCIÓN, no su retorno
Alpine.data('loginForm', loginForm);
Alpine.data('registerForm', registerForm);

Alpine.start();
