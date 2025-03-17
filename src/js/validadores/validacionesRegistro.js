/**
 * validación de formularios del Registro
 */

import { validarVacio } from "./validaciones.js";
import { validarEmail } from "./validacionesLogin.js";

/**
 * Valida que la contraseña cumpla con los requisitos de seguridad:
 * - Al menos 8 caracteres.
 * - Al menos una letra minúscula.
 * - Al menos una letra mayúscula.
 * - Al menos un número.
 * - Al menos un carácter especial (!@#$%^&*()_+-/.).
 * 
 * @param {string} password - La contraseña a validar.
 * @returns {boolean} - `true` si la contraseña es válida, `false` en caso contrario.
 */
export function validarFormatoPassword(password) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+.\-]).{8,}$/;
    return regex.test(password);
}


export function validarPasswordRegister(password) {
    return !validarVacio(password) && validarFormatoPassword(password);
}


export function validarFormularioRegistro(email, password) {
    return validarEmail(email) && validarPasswordRegister(password);
}

export function validarNumeroTelefonico(telefono) {
    const regex = /^\+52\d{10}$/;
    return regex.test(telefono);
}