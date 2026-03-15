<?php

namespace App\Http\Requests;

/**
 * Clase centralizada para mensajes de validación personalizados.
 * Facilita la gestión de mensajes de error en toda la aplicación.
 */
class ValidationMessages
{
    /**
     * Obtiene los atributos personalizados (nombres en español).
     *
     * @return array
     */
    public static function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'firstName' => 'nombre',
            'lastName' => 'apellido',
        ];
    }

    /**
     * Obtiene los mensajes de validación personalizados.
     *
     * @return array
     */
    public static function messages(): array
    {
        return [
            // Email
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser válido.',
            'email.exists' => 'El correo no se encuentra registrado.',
            'email.unique' => 'El correo ya se encuentra registrado.',

            // Contraseña
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            // Nombre
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',

            // Campos adicionales
            'firstName.required' => 'El nombre es obligatorio.',
            'firstName.string' => 'El nombre debe ser texto.',
            'lastName.required' => 'El apellido es obligatorio.',
            'lastName.string' => 'El apellido debe ser texto.',
        ];
    }

    /**
     * Obtiene los atributos para un contexto específico (ej: login).
     *
     * @param string $context Contexto de validación (login, register, profile, etc)
     * @return array
     */
    public static function getAttributes(string $context = 'common'): array
    {
        $allAttributes = self::attributes();

        $contexts = [
            'login' => ['email', 'password'],
            'register' => ['name', 'email', 'password', 'password_confirmation'],
            'signup' => ['firstName', 'lastName', 'email', 'password', 'password_confirmation'],
            'profile' => ['name', 'email'],
        ];

        if (!isset($contexts[$context])) {
            return $allAttributes;
        }

        return array_filter(
            $allAttributes,
            fn($key) => in_array($key, $contexts[$context]),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Obtiene los mensajes para un contexto específico.
     *
     * @param string $context Contexto de validación
     * @return array
     */
    public static function getMessages(string $context = 'common'): array
    {
        return self::messages();
    }

    /**
     * Obtiene tanto atributos como mensajes para un contexto.
     *
     * @param string $context Contexto de validación
     * @return array
     */
    public static function getAll(string $context = 'common'): array
    {
        return [
            'attributes' => self::getAttributes($context),
            'messages' => self::getMessages($context),
        ];
    }
}
