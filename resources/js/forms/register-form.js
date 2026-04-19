/**
 * Register Form - Alpine.js Component
 * Maneja la lógica de registro de nuevos usuarios
 */
export function registerForm() {
    return {
        form: {
            firstName: '',
            lastName: '',
            email: '',
            password: '',
            passwordConfirmation: '',
            agreeTerms: false,
        },
        errors: [],
        isLoading: false,

        /**
         * Maneja el envío del formulario de registro
         * - Valida que acepte términos y condiciones
         * - Crea nuevo usuario en API
         * - Redirige a login con mensaje de éxito
         */
        async handleRegister() {
            this.errors = [];

            // Validación local: términos y condiciones
            if (!this.form.agreeTerms) {
                this.errors.push('Debes aceptar los términos y condiciones.');
                return;
            }

            this.isLoading = true;

            try {
                // Llamar al API de registro
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name: `${this.form.firstName} ${this.form.lastName}`,
                        email: this.form.email,
                        password: this.form.password,
                        password_confirmation: this.form.passwordConfirmation,
                    }),
                });

                const data = await response.json();

                // Manejar errores de validación
                if (!response.ok) {
                    this.handleRegisterErrors(data);
                    return;
                }

                // Registro exitoso - redirigir a login
                if (data.accessToken) {
                    this.redirectToLogin();
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors = ['Error de conexión. Por favor, intenta de nuevo.'];
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Maneja los errores de validación del registro
         */
        handleRegisterErrors(data) {
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
         * Redirige a login con mensaje de éxito
         */
        redirectToLogin() {
            const successMessage = 'El usuario ha sido creado exitosamente. Por favor, inicie sesión para continuar.';
            sessionStorage.setItem('registration_success', successMessage);
            window.location.href = '/signin';
        }
    };
}
