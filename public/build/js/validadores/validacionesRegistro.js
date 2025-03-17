import{validarVacio}from"./validaciones.js";import{validarEmail}from"./validacionesLogin.js";export function validarFormatoPassword(r){return/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+.\-]).{8,}$/.test(r)}export function validarPasswordRegister(r){return!validarVacio(r)&&validarFormatoPassword(r)}export function validarFormularioRegistro(r,a){return validarEmail(r)&&validarPasswordRegister(a)}export function validarNumeroTelefonico(r){return/^\+52\d{10}$/.test(r)}