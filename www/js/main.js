import { verificarSesion, iniciarLogin } from "./autenticacion.js";
import { iniciarSubirFicheros } from "./subirFicheros.js";
import { cargarActas } from "./actas.js";

const divLogin = document.getElementById("divLogin");
const divBienvenida = document.getElementById("divBienvenida");
const main = document.querySelector("main");
const modalLogin = document.getElementById('modalLogin');
const modalGrupos = document.getElementById("modalGrupos");
const formularioLogin = document.getElementById('formularioLogin');

window.onload = function() {
    verificarSesion()
    .then(res => {
        if(res && res.html){
            cargarAplicacion(res.html);
        }else{
            mostrarLogin();
        }
    })
    .catch(err => console.error("Error verificando sesión: ", err));

    configurarLogin();
}

function mostrarLogin(){
    divLogin.style.display = "block";
    divBienvenida.style.display = "none";
}

function cargarAplicacion(html){
    divLogin.style.display = "none";
    divBienvenida.style.display = "block";
    main.innerHTML = html;
    configurarContenedores();
}

function configurarLogin(){
    formularioLogin.addEventListener("submit", (e) => {
        e.preventDefault();
        const datos = new FormData(formularioLogin);

        iniciarLogin(datos)
        .then(data => {
            if(data.success && data.html){
                bootstrap.Modal.getInstance(modalLogin).hide();
                cargarAplicacion(data.html);
            }
        })
        .catch(() => {
            const msgError = document.getElementById("mensajeErrorLogin");
            msgError.style.display = "block";
            setTimeout(() => msgError.style.display = "none", 5000);
        })
    })
}

// Pasa los datos del html
function configurarContenedores(){
    iniciarSubirFicheros({
        idContenedor: 'contenedorDropdown1',
        idInput: 'inputFicheroAlumnos',
        idMensajes: 'mensajesDropdown1',
        idLabel: 'labelDropdown1',
        idBorrar: 'divBorrarAlumnos',
        textoError: 'Error: Solo archivos XML.',
        icono: 'xml.png',
        validarArchivo: (f) => f.name.endsWith(".xml") || f.type === 'text/xml'
    });

    iniciarSubirFicheros({
        idContenedor: 'contenedorDropdown2',
        idInput: 'inputFicheroProfesores',
        idMensajes: 'mensajesDropdown2',
        idLabel: 'labelDropdown2',
        idBorrar: 'divBorrarProfesores',
        textoError: 'Error: Solo archivos XLSX.',
        icono: 'xlsx.png',
        validarArchivo: (f) => f.name.endsWith('.xlsx') || f.type.includes('spreadsheet')
    });

    const btnGenerar = document.getElementById('generarActa');
    const grupos = document.getElementById("grupos");
    let archivoFetch = ""
    if(btnGenerar){
        btnGenerar.addEventListener('click', () => {
            const formData = new FormData();

            const profesores = document.getElementById("inputFicheroProfesores").files[0];
            archivoFetch = profesores;

            if(profesores){
                formData.append('profesores', profesores);

                fetch("../php/parseXLSX.php", {
                    method: 'POST',
                    body: formData
                })
                .then(res => {
                    if(!res.ok) throw new Error("Error a la hora de leer los ficheros");
                    return res.json();
                })
                .then(data => {
                    bootstrap.Modal.getOrCreateInstance(modalGrupos).show();

                    for(let [indice, grupo] of data.entries()){
                        let opcion = document.createElement("option");
                        opcion.innerText = `Grupo: ${grupo.grup} - Tutor: ${grupo.tutor}`; 
                        opcion.setAttribute("value", indice);
                        grupos.appendChild(opcion);
                    }
                })
                .catch(err => alert(err));
            }else alert("No has subido fichero o no son válidos");  
        })
    }

    const submitGrupos = document.getElementById("submitGrupos");
    submitGrupos.addEventListener("click", (e) => {
        e.preventDefault();
        let opcion = Number(grupos.value);
        if(opcion !== -1){
            const formData = new FormData();
            formData.append('grupo', opcion);
            formData.append('profesores', archivoFetch);
            fetch("../php/fillTEMPLATE.php", {
                method: 'POST',
                body: formData
            })
            .then(res => {
                const contenedorActas = document.getElementById("contenedorActas");
                bootstrap.Modal.getInstance(modalGrupos).hide();
                contenedorActas.classList.remove("d-none");
                cargarActas();
            })
            .catch(err => alert(err));
        }
    });
}