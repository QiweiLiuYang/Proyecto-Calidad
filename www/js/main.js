let formulario = document.getElementById("formularioLogin");
    let botonSubmit = document.getElementById("botonSubmit");

    botonSubmit.addEventListener("click", function(e){
        e.preventDefault();
        let usuario = document.getElementById("usuario").value;
        let contrasena = document.getElementById("contrasena").value;
        
        let datos = new FormData(formulario);
        fetch('../php/login.php', {
            method: 'POST',
            body: datos
        })
        .then(res => {
            if(res.ok){
                return res.json();
            } else {
                throw new Error('Error en la solicitud');
            }
        })
        .then(data => {
            console.log(data);
            if(data.success){
                let divLogin = document.getElementById("divLogin");
                divLogin.style.display = "none";
                let botonLogin = document.getElementById("botonLogin");
                botonLogin.style.display = "none";
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalLogin'));
                modal.hide();

                let divBienvenida = document.getElementById("divBienvenida");
                divBienvenida.style.display = "block";
            }
        })
        .catch(err => {
            console.error('Error:', err);
            let mensajeErrorLogin = document.getElementById("mensajeErrorLogin");
            mensajeErrorLogin.style.display = "block";
            setTimeout(() => {
                mensajeErrorLogin.style.display = "none";
            }, 5000)
        })
    });