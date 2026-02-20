export function cargarActas(){
    fetch("../php/devolverActas.php")
    .then(res => {
        if(!res.ok) throw new Error("Error a la hora de recuperar las actas");
        return res.json();
    })
    .then(data => {
        console.log(data);
        const divActas = document.getElementById("divActas");
        for(let uri of data){
            const div = document.createElement("div");
            div.classList.add("d-flex", "justify-content-evenly", "align-items-center", "border-bottom", "pb-2");
            div.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <img src="../img/xlsx.png" alt="Icono de excel" width="75px" heigth="auto">
                    <span>${uri.substring(13)}</span>
                </div>
                <div>
                    <a href="${uri}" download="${uri.substring(13)}" class="btn bg-transparent border rounded-3 border-bg-pure-black">    
                        <img src="../img/descargar.png" alt="Icono de descarga" width="50px" height="auto">
                    </a>
                </div>
                <div>
                    <button type="button" class="btn bg-transparent border rounded-3 border-bg-pure-black">
                        <img src="../img/papelera.png" alt="Icono de descarga" width="50px" height="auto">
                    </button>
                </div>
            `;
            divActas.appendChild(div);
        }
    })
    .catch(err => console.error(err));
}