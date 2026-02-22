// Function to display the "generate minutes" button when both files have been uploaded
function toggleGenerarActa(){
    const divGenerarActa = document.getElementById('divGenerarActa');
    const inputAlumnos = document.getElementById('inputFicheroAlumnos');
    const inputProfesores = document.getElementById('inputFicheroProfesores');

    if(divGenerarActa && inputAlumnos && inputProfesores){
        if(inputAlumnos.disabled && inputProfesores.disabled) divGenerarActa.classList.remove('d-none');
        else divGenerarActa.classList.add('d-none');
    }
}

// Function for container logic and file validation
export function iniciarSubirFicheros(datos){
    // Data passed from main.js to reuse the function with both containers
    const {idContenedor, idInput, idMensajes, idLabel, idBorrar, validarArchivo, icono, textoError} = datos;

    const contenedor = document.getElementById(idContenedor);
    const input = document.getElementById(idInput);
    const mensajes = document.getElementById(idMensajes);
    const label = document.getElementById(idLabel);
    const divBorrar = document.getElementById(idBorrar);
    const btnBorrar = divBorrar.querySelector('button');
    let timeout;
    let accionesNavegador = ['dragenter', 'dragover', 'dragleave', 'drop'];

    // Prevent default actions
    accionesNavegador.forEach(accion => {
        contenedor.addEventListener(accion, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Add a red border when a file is passed over the container
    const toggleBorde = (rojo) => contenedor.classList.toggle('border-red', rojo);
    contenedor.addEventListener('dragenter', () => toggleBorde(true));
    contenedor.addEventListener('dragover', () => toggleBorde(true));
    contenedor.addEventListener('dragleave', () => toggleBorde(false));
    contenedor.addEventListener('drop', () => {toggleBorde(false)});


    // Validate the file when dropping
    contenedor.addEventListener('drop', (e) => {
        const archivos = e.dataTransfer.files;
        if(archivos.length == 1){
            input.files = archivos;
            validar(archivos[0]);
        }else{
            mostrarError('Por favor, suba solo un fichero.');
        }
    });

    // Validate the file by selecting it from the input
    input.addEventListener('change', () => {
        if(input.files.length > 0) validar(input.files[0]);
    });

    // Functionality of the delete file button
    btnBorrar.addEventListener('click', () => {
        input.value = '';
        input.disabled = false;
        mensajes.innerHTML = '';
        label.classList.remove('d-none');
        contenedor.parentElement.classList.remove('flex-grow-1');
        divBorrar.classList.add('d-none');
        toggleGenerarActa();
    });

    // Validate the file and display corresponding messages
    function validar(archivo){
        clearTimeout(timeout);
        if(validarArchivo(archivo)){
            mensajes.innerHTML = `
                <img src="../img/${icono}" width="50px" height="auto"><br>
                <span>${archivo.name}</span>
            `;
            mensajes.classList.remove('text-danger');
            label.classList.add('d-none');
            input.disabled = true;
            contenedor.parentElement.classList.add('flex-grow-1');
            divBorrar.classList.remove('d-none');
            toggleGenerarActa();
        }else{
            input.value = '';
            mostrarError(textoError);
        }
    }

    // Function to show error message and dissapears in 5 seconds
    function mostrarError(texto){
        mensajes.textContent = texto;
        mensajes.classList.add('text-danger');
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            mensajes.textContent = '';
            mensajes.classList.remove('text-danger');
        }, 5000);
    }
}