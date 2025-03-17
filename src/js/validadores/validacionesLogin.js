/**
 * validación de formularios del login
 * 
 */

import { validarVacio } from "./validaciones.js";

export function validarEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}

export function validarPassword(password) {
    return !validarVacio(password);
}

export function validarFormulario(email, password) {
    return validarEmail(email) && validarPassword(password);
}