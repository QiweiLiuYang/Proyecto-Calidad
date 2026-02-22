// Function that main.js will use to check session status when the page loads
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

// Function to handle the login form
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

