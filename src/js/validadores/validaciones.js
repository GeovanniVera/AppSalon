// Módulo de validación -> Encargado de validar los campos del formulario
/**
 * Validaciones generales de formularios
 */

export function validarFormatoAlfabetico(value) {
    const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑüÜ]+(?: [A-Za-záéíóúÁÉÍÓÚñÑüÜ]+)*$/;
    return regex.test(value.trim());
}

export function validarVacio(value) {
    return value.trim() === '';
}

