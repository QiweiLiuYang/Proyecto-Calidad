// Función para mostrar el botón de generar acta cuando se han subido ambos archivos
function toggleGenerarActa(){
    const divGenerarActa = document.getElementById('divGenerarActa');
    const inputAlumnos = document.getElementById('inputFicheroAlumnos');
    const inputProfesores = document.getElementById('inputFicheroProfesores');

    if(divGenerarActa && inputAlumnos && inputProfesores){
        if(inputAlumnos.disabled && inputProfesores.disabled) divGenerarActa.classList.remove('d-none');
        else divGenerarActa.classList.add('d-none');
    }
}

// Función para la lógica de los contenedores y validación de los archivos
export function iniciarSubirFicheros(datos){
    // Datos que se pasan desde el main.js para reutilizar la función con ambos contenedores
    const {idContenedor, idInput, idMensajes, idLabel, idBorrar, validarArchivo, icono, textoError} = datos;

    const contenedor = document.getElementById(idContenedor);
    const input = document.getElementById(idInput);
    const mensajes = document.getElementById(idMensajes);
    const label = document.getElementById(idLabel);
    const divBorrar = document.getElementById(idBorrar);
    const btnBorrar = divBorrar.querySelector('button');
    let timeout;
    let accionesNavegador = ['dragenter', 'dragover', 'dragleave', 'drop'];

    // Prevenir las acciones por defecto
    accionesNavegador.forEach(accion => {
        contenedor.addEventListener(accion, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Agregar borde rojo cuando se pasa un fichero por encima del contenedor
    const toggleBorde = (rojo) => contenedor.classList.toggle('border-red', rojo);
    contenedor.addEventListener('dragenter', () => toggleBorde(true));
    contenedor.addEventListener('dragover', () => toggleBorde(true));
    contenedor.addEventListener('dragleave', () => toggleBorde(false));
    contenedor.addEventListener('drop', () => {toggleBorde(false)});


    // Validar el archivo al hacer drop
    contenedor.addEventListener('drop', (e) => {
        const archivos = e.dataTransfer.files;
        if(archivos.length == 1){
            input.files = archivos;
            validar(archivos[0]);
        }else{
            mostrarError('Por favor, suba solo un fichero.');
        }
    });

    // Validar el archivo al seleccionarlo desde el input
    input.addEventListener('change', () => {
        if(input.files.length > 0) validar(input.files[0]);
    });

    // Funcionalidad del botón de borrar archivo
    btnBorrar.addEventListener('click', () => {
        input.value = '';
        input.disabled = false;
        mensajes.innerHTML = '';
        label.classList.remove('d-none');
        contenedor.parentElement.classList.remove('flex-grow-1');
        divBorrar.classList.add('d-none');
        toggleGenerarActa();
    });

    // Validar el archivo y mostrar mensajes correspondientes
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