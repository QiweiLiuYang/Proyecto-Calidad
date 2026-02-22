// Import functions from another files
import { verificarSesion, iniciarLogin } from "./autenticacion.js";
import { iniciarSubirFicheros } from "./subirFicheros.js";
import { cargarActas } from "./actas.js";

// DOM references for global access throughout the script
const divLogin = document.getElementById("divLogin");
const divBienvenida = document.getElementById("divBienvenida");
const main = document.querySelector("main");
const modalLogin = document.getElementById('modalLogin');
const modalGrupos = document.getElementById("modalGrupos");
const formularioLogin = document.getElementById('formularioLogin');

// Functions to execute when the window finishes loading
window.onload = function() {
    // Check if a user session already exists
    verificarSesion()
    .then(res => {
        // If session already exist and is valid, skip login and load the app content
        if(res && res.html){
            cargarAplicacion(res.html);
        }else{
            // Otherwise, show the login interface
            mostrarLogin();
        }
    })
    .catch(err => console.error("Error verificando sesión: ", err)); // Print any errors to the console

    // Setup the event listener for the login form
    configurarLogin();
}

// Function to show the login interface
function mostrarLogin(){
    divLogin.style.display = "block";
    divBienvenida.style.display = "none";
}

// Function to initialize the application
function cargarAplicacion(html){
    divLogin.style.display = "none";
    divBienvenida.style.display = "block";
    main.innerHTML = html;

    // Initialize file upload containers and listeners
    configurarContenedores();

    // Fetch and display existing records (actas)
    cargarActas();
}

// Handles the login form logic
function configurarLogin(){
    formularioLogin.addEventListener("submit", (e) => {
        e.preventDefault();
        const datos = new FormData(formularioLogin);

        iniciarLogin(datos)
        .then(data => {
            if(data.success && data.html){
                // Hide the login modal and load the app
                bootstrap.Modal.getInstance(modalLogin).hide();
                cargarAplicacion(data.html);
            }
        })
        .catch(() => {
            // Display error message for 5 seconds and then dissapears
            const msgError = document.getElementById("mensajeErrorLogin");
            msgError.style.display = "block";
            setTimeout(() => msgError.style.display = "none", 5000);
        })
    })
}

// Set up the file input and generation logic
function configurarContenedores(){
    // Configure input for students (XML files)
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

    // Configure input for teachers (XLSX files)
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

    // Declare variables and get DOM elements
    const btnGenerar = document.getElementById('generarActa');
    const grupos = document.getElementById("grupos");
    let archivoFetchExcel = "";
    let archivoFetchXML = "";

    // Logic to send files to the server and show a modal to select groups
    if(btnGenerar){
        btnGenerar.addEventListener('click', (e) => {
            e.target.disabled = true;

            const formData = new FormData();

            const alumnos = document.getElementById("inputFicheroAlumnos").files[0];
            const profesores = document.getElementById("inputFicheroProfesores").files[0];
            archivoFetchXML = alumnos;
            archivoFetchExcel = profesores;

            if(alumnos && profesores){
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
                    // Display the modal
                    bootstrap.Modal.getOrCreateInstance(modalGrupos).show();

                    e.target.disabled = false;

                    grupos.innerHTML = '<option value="-1" selected>Selecciona un grupo...</option>';

                    // Fill the select with options
                    for(let [indice, grupo] of data.entries()){
                        let opcion = document.createElement("option");
                        opcion.innerText = `Grupo: ${grupo.grup} - Tutor: ${grupo.tutor}`; 
                        opcion.setAttribute("value", indice);
                        grupos.appendChild(opcion);
                    }
                })
                .catch(err => {
                    alert(err);
                    e.target.disabled = false;
                });
            }else {
                alert("No has subido fichero o no son válidos")
                e.target.disabled = false;
            }
        })
    }

    // Logic to ask server to generate and get XLSX files
    const submitGrupos = document.getElementById("submitGrupos");
    submitGrupos.addEventListener("click", (e) => {
        e.preventDefault();

        let opcion = Number(grupos.value);

        // If the select placeholder is not changed, do nothing
        if(opcion !== -1){
            console.log(opcion);

            e.target.disabled = true;

            const cargando = document.getElementById("cargando");
            cargando.classList.remove("d-none");
            cargando.classList.add("d-flex");

            const formData = new FormData();
            formData.append('grupo', opcion);
            formData.append('profesores', archivoFetchExcel);
            formData.append('alumnos', archivoFetchXML);

            fetch("../php/fillTEMPLATE.php", {
                method: 'POST',
                body: formData
            })
            .then(res => {
                const contenedorActas = document.getElementById("contenedorActas");
                const borrarEstudiantes = document.getElementById("borrarEstudiantes");
                const borrarProfesores = document.getElementById("borrarProfesores");

                borrarEstudiantes.click();
                borrarProfesores.click();

                bootstrap.Modal.getInstance(modalGrupos).hide();
                contenedorActas.classList.remove("d-none");
                cargarActas();
            })
            .catch(err => alert(err))
            .finally(() => {
                cargando.classList.remove("d-flex");
                cargando.classList.add("d-none");
                e.target.disabled = false;
            });
        }
    });
}