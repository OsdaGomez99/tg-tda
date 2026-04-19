/**
 * Formulario de Acceso - Componente Alpine.js
 * Maneja la lógica de autenticación y sesión
 */
export function loginForm() {
    return {
        form: {
            email: '',
            password: '',
            rememberMe: false,
        },
        errors: [],
        successMessage: '',
        isLoading: false,

        /**
         * Inicializa el formulario
         * Verifica si hay mensaje de registro exitoso en sessionStorage
         */
        init() {
            const registrationSuccess = sessionStorage.getItem('registration_success');
            if (registrationSuccess) {
                this.successMessage = registrationSuccess;
                sessionStorage.removeItem('registration_success');
                // Auto-hide mensaje después de 5 segundos
                setTimeout(() => {
                    this.successMessage = '';
                }, 5000);
            }
        },

        /**
         * Maneja el envío del formulario de login
         * - Valida credenciales contra API
         * - Guarda token y datos en localStorage
         * - Guarda datos en sesión del servidor
         * - Redirige al dashboard
         */
        async handleLogin() {
            this.errors = [];
            this.isLoading = true;

            try {
                const email = this.form.email;
                const password = this.form.password;

                // Llamar al API de login
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                    }),
                });

                const data = await response.json();

                // Manejar errores de validación
                if (!response.ok) {
                    this.handleLoginErrors(data);
                    return;
                }

                // Login exitoso - guardar token y sesión
                if (data.accessToken) {
                    await this.saveLoginData(data);
                    window.location.href = '/';
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors = ['Error de conexión. Por favor, intenta de nuevo.'];
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Maneja los errores de validación del login
         */
        handleLoginErrors(data) {
            if (data.errors) {
                if (Array.isArray(data.errors)) {
                    this.errors = data.errors;
                } else if (typeof data.errors === 'object') {
                    this.errors = Object.values(data.errors).flat();
                } else {
                    this.errors = [data.errors];
                }
            } else if (data.message) {
                this.errors = [data.message];
            } else {
                this.errors = ['Ocurrió un error. Por favor, intenta de nuevo.'];
            }
        },

        /**
         * Guarda el token y datos en localStorage y sesión del servidor
         */
        async saveLoginData(data) {
            const token = data.accessToken;

            // Guardar en localStorage (persistencia en cliente)
            localStorage.setItem('auth_token', token);
            localStorage.setItem('user_name', data.fullname);
            localStorage.setItem('user_email', data.email);

            // Guardar en sesión del servidor
            try {
                await fetch('/api/auth/store-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                    },
                });

                // Esperar para asegurar que la sesión se guarde
                await new Promise(resolve => setTimeout(resolve, 500));
            } catch (error) {
                console.error('Error guardando sesión:', error);
                // Continuar incluso si falla (localStorage ya tiene datos)
            }
        }
    };
}
