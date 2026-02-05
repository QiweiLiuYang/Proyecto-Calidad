window.onload = function(){
    let formulario = document.getElementById("formularioLogin");

    formulario.addEventListener("submit", function(e){
        e.preventDefault();
        
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
                document.getElementById("divLogin").style.display = "none";

                bootstrap.Modal.getInstance(document.getElementById('modalLogin')).hide();

                document.getElementById("divBienvenida").style.display = "block";
            }
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById("mensajeErrorLogin").style.display = "block";

            setTimeout(() => {
                document.getElementById("mensajeErrorLogin").style.display = "none";
            }, 5000)
        })
    });

    fetch('../php/sessionStatus.php')
    .then(res => res.json())
    .then(data => {
        if(data.logged){
            document.getElementById("divBienvenida").style.display = "block";
            document.getElementById("divLogin").style.display = "none";
        }else{
            document.getElementById('divLogin').style.display = 'block';
            document.getElementById('divBienvenida').style.display = 'none';
        }
    });
}
