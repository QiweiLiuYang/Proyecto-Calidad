// Función que usará main.js para verificar el estado de la sesión al cargar la página
export function verificarSesion(){
    return fetch('../php/sessionStatus.php')
    .then(res => res.json())
    .then(data => {
        if(data.logged){
            return fetch('../php/checkLogin.php').then(r => r.json());
        }
        return null;
    });
}

// Función para manejar el formulario de login
export function iniciarLogin(datosFormulario){
    return fetch('../php/login.php', {
        method: 'POST',
        body: datosFormulario
    })
    .then(res => {
        if(!res.ok) throw new Error("Error en la solicitud");
        return res.json();
    });
}

