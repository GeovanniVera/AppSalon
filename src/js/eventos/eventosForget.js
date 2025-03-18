/*
Módulo de manejo de eventos
Encargado de gestionar los eventos del formulario.
*/

import { validarVacio } from "../validadores/validaciones.js";
import { validarEmail } from "../validadores/validacionesLogin.js";
import { habilitarBoton, mostrarError, mostrarExito } from "../doms/dom.js";

const emailInput = document.getElementById('email');

export function inicializarEventos() {
    emailInput.addEventListener('input', validarEmailEnTiempoReal);
    document.querySelector('#forgetForm').addEventListener('submit', manejarSubmit);
    window.addEventListener('load', () => habilitarBoton('forgetForm',false)); // Deshabilitar botón al cargar
}

//Validar email en tiempo real se puede extraer en el futuro para siimplificar 
// pues se utiliza de forma repetitiva en varias funciones 
function validarEmailEnTiempoReal() {
    if (validarVacio(emailInput.value)) {
        mostrarError('email', 'Porfavor Ingresa un email.');
    } else if (!validarEmail(emailInput.value)) {
        mostrarError('email', 'El email no cumple con un formato válido.');
    } else {
        mostrarExito('email');
    }
    actualizarEstadoBoton();
}

function manejarSubmit(event) {
    let errors = 0;

    if (!validarEmail(emailInput.value)) {
        mostrarError('email', 'Por favor, ingresa un correo válido.');
        errors++;
    } else {
        mostrarExito('email');
    }


    if (errors > 0) {
        event.preventDefault();
    }
}

function actualizarEstadoBoton() {
    habilitarBoton('forgetForm',sonTodosLosCamposValidos);
}

function sonTodosLosCamposValidos() {
    const emailValido = validarEmail(emailInput.value);
    return  emailValido;
}