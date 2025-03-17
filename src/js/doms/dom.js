/* 
    Módulo de manipulación del DOM
    Encargado de mostrar/ocultar errores y habilitar/deshabilitar el botón de envío.
*/

/**
 * 
 * @param {string} campo 
 * @param {string} mensaje 
 * document.getElementById(campo).classList.remove('exito_border'); -> last_name
 * 
 */
export function mostrarError(campo, mensaje) {
    document.getElementById(campo).classList.remove('exito_border');
    document.querySelector('#label-' + campo).classList.remove('exito-label');
    document.getElementById(campo).classList.add('error_border');
    document.querySelector('#label-' + campo).classList.add('error-label');
    document.getElementById(campo + 'Error').textContent = 'ⓘ ' + mensaje;
}
/**
 * 
 * @param {string} campo 
 */
export function mostrarExito(campo) {
    document.getElementById(campo).classList.remove('error_border');
    document.querySelector('#label-' + campo).classList.remove('error-label');
    document.getElementById(campo).classList.add('exito_border');
    document.querySelector('#label-' + campo).classList.add('exito-label');
    document.getElementById(campo + 'Error').textContent = '';
}

/**
 * 
 * @param {boolean} habilitar 
 */
export function habilitarBotonRegister(habilitar) {
    const submitButton = document.querySelector('#registerForm input[type="submit"]');
    if (submitButton) {
        submitButton.disabled = !habilitar;
    }
}

/**
 * Habilita o deshabilita el botón de envío de un formulario.
 * @param {string} formId El ID del formulario.
 * @param {boolean} habilitar Indica si el botón debe estar habilitado (true) o deshabilitado (false).
 */
export function habilitarBoton(formId, habilitar) {
    const form = document.querySelector(`#${formId}`);
    if (form) {
        const submitButton = form.querySelector('input[type="submit"]');
        if (submitButton) {
            submitButton.disabled = !habilitar;
        }
    }
}