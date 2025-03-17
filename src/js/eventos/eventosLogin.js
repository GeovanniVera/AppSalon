/*
Módulo de manejo de eventos
Encargado de gestionar los eventos del formulario.
*/

import { validarEmail, validarPassword, validarFormulario } from '../validadores/validacionesLogin.js';
import { mostrarError, mostrarExito, habilitarBoton } from '../doms/dom.js';

const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

export function inicializarEventos() {
    emailInput.addEventListener('input', validarEmailEnTiempoReal);
    passwordInput.addEventListener('input', validarPasswordEnTiempoReal);
    document.querySelector('#loginForm').addEventListener('submit', manejarSubmit);
    window.addEventListener('load', () => habilitarBoton('loginForm',false)); // Deshabilitar botón al cargar
}

function validarEmailEnTiempoReal() {
    if (validarEmail(this.value)) {
        mostrarExito('email');
        passwordInput.disabled = false;
    } else {
        mostrarError('email', 'El email no cumple con un formato válido.');
        passwordInput.disabled = true;
    }
    actualizarEstadoBoton();
}

function validarPasswordEnTiempoReal() {
    const password = this.value;
    if (password.trim() === '') {
        mostrarError('password', 'Por favor, ingresa tu contraseña.');
    } else {
        mostrarExito('password');
    }
    actualizarEstadoBoton();
}

function manejarSubmit(event) {
    const email = emailInput.value;
    const password = passwordInput.value;
    let errors = 0;

    if (email.trim() === '') {
        mostrarError('email', 'Por favor, ingresa tu correo electrónico.');
        errors++;
    } else if (!validarEmail(email)) {
        mostrarError('email', 'El email no cumple con un formato válido.');
        errors++;
    } else {
        mostrarExito('email');
    }

    if (password.trim() === '') {
        mostrarError('password', 'Por favor, ingresa tu contraseña.');
        errors++;
    } else {
        mostrarExito('password');
    }

    if (errors > 0) {
        event.preventDefault();
        return;
    }
}

function actualizarEstadoBoton() {
    habilitarBoton('loginForm',sonTodosLosCamposValidos());
}

function sonTodosLosCamposValidos() {
    const emailValido = validarEmail(emailInput.value);
    const passwordValido = validarPassword(passwordInput.value);
    return  emailValido && passwordValido;
}