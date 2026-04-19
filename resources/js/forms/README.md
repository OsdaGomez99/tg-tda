# Estructura de JavaScript en el Proyecto

## Carpeta: `resources/js/forms/`

Contiene módulos ES6 para los formularios de autenticación.

### Archivos

#### `login-form.js`

- **Función:** `loginForm()`
- **Responsabilidad:** Lógica del formulario de login
- **Métodos principales:**
    - `init()` - Inicializa el formulario y verifica mensajes de registro
    - `handleLogin()` - Procesa el envío del formulario
    - `handleLoginErrors()` - Maneja errores de validación
    - `saveLoginData()` - Guarda token y datos en localStorage/sesión

**Flujo:**

1. Usuario ingresa credenciales
2. Envía POST a `/api/auth/login`
3. Si es exitoso:
    - Guarda token en localStorage
    - Llama a `/api/auth/store-session` para guardar en servidor
    - Redirige a dashboard
4. Si hay error, muestra validación

#### `register-form.js`

- **Función:** `registerForm()`
- **Responsabilidad:** Lógica del formulario de registro
- **Métodos principales:**
    - `handleRegister()` - Procesa el envío del formulario
    - `handleRegisterErrors()` - Maneja errores de validación
    - `redirectToLogin()` - Redirige a login con mensaje de éxito

**Flujo:**

1. Usuario completa todos los campos
2. Acepta términos y condiciones
3. Envía POST a `/api/auth/register`
4. Si es exitoso:
    - Guarda mensaje de éxito en sessionStorage
    - Redirige a login
5. Si hay error, muestra validación

## Vistas que Usan Estos Módulos

### `/resources/views/pages/auth/signin.blade.php`

```html
<script type="module">
    import { loginForm } from "/js/forms/login-form.js";
    window.loginForm = loginForm;
</script>
```

### `/resources/views/pages/auth/signup.blade.php`

```html
<script type="module">
    import { registerForm } from "/js/forms/register-form.js";
    window.registerForm = registerForm;
</script>
```

## Cómo Funciona

1. **Importación ES6 Modules:** Usa `import/export` para módulos
2. **Global Binding:** Los módulos se asignan a `window` para acceso desde Alpine.js
3. **Alpine.js Integration:** Los formularios usan `x-data="loginForm()"` o `x-data="registerForm()"`

## Ventajas de Esta Estructura

✅ **Separación de responsabilidades** - JavaScript separado de vistas  
✅ **Reutilizable** - Los módulos pueden usarse en múltiples vistas  
✅ **Mantenible** - Código organizado y documentado  
✅ **Testeable** - Funciones puras sin dependencias de HTML  
✅ **Performance** - Módulos ES6 optimizados por navegadores modernos

## Futuras Mejoras

- Agregar validación adicional en cliente
- Implementar debouncing en inputs
- Agregar testing unitario
- Crear módulo de API client reutilizable
