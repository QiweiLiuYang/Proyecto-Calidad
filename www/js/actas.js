// Fetches the list of generated ZIP files from the server and renders them dynamically in the interface
export function cargarActas(){
    // Request the list of available files from the backend
    fetch("../php/devolverActas.php")
    .then(res => {
        // Basic error handling for the network request
        if(!res.ok) throw new Error("Error a la hora de recuperar las actas");
        return res.json();
    })
    .then(data => {
        const divActas = document.getElementById("divActas");

        // Clear existing content to prevent duplicates on refresh
        divActas.innerHTML = "";

        // Iterate through each file path (URI) returned by PHP
        for(let uri of data){
            const div = document.createElement("div");
            div.classList.add("d-flex", "justify-content-evenly", "align-items-center", "border-bottom", "pb-2");
            div.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <img src="../img/zip.png" alt="Icono de excel" width="75px" height="auto">
                    <span>${uri.substring(13)}</span>
                </div>
                <div>
                    <a href="${uri}" download="${uri.substring(13)}" class="btn bg-transparent border rounded-3 border-bg-pure-black">    
                        <img src="../img/descargar.png" alt="Icono de descarga" width="50px" height="auto">
                    </a>
                </div>
                <div>
                    <button type="button" class="btn bg-transparent border rounded-3 border-bg-pure-black" data-ruta="${uri.substring(13)}">
                        <img src="../img/papelera.png" alt="Icono de descarga" width="50px" height="auto">
                    </button>
                </div>
            `;

            // Setup the delete buttom logic and functionality
            const botonBorrar = div.querySelector("button");
            botonBorrar.addEventListener("click", (e) => {
                const formData = new FormData();
                formData.append("acta", e.currentTarget.getAttribute("data-ruta"));

                fetch("../php/borrarActas.php", {
                    method: 'POST',
                    body: formData
                })
                .then(res => {if(res.ok) cargarActas()})
                .catch(err => console.error(err));
            });

            divActas.appendChild(div);
        }
    })
    .catch(err => console.error(err));
}