window.onload = function(){
    fetch('../php/sessionStatus.php')
    .then(res => res.json())
    .then(data => {
        if(data.logged){
            document.getElementById("divBienvenida").style.display = "block";
            document.getElementById("divLogin").style.display = "none";

            document.getElementsByTagName("main")[0].innerHTML = data.html;
        }else{
            document.getElementById('divLogin').style.display = 'block';
            document.getElementById('divBienvenida').style.display = 'none';
        }
    });

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

                document.getElementsByTagName("main")[0].innerHTML = data.html;
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
}

// Función para hacer el drag and drop de los contenedores de los archivos

const contenedorDropdown1 = document.getElementById('contenedorDropdown1');
const inputFicheroAlumnos = document.getElementById('inputFicheroAlumnos');
const mensajesDropdown1 = document.getElementById('mensajesDropdown1');
const labelDropdown1 = document.getElementById('labelDropdown1');
const divBorrarAlumnos = document.getElementById('divBorrarAlumnos');
let timeoutAlumnos;

let accionesNavegador = ['dragenter', 'dragover', 'dragleave', 'drop'];
accionesNavegador.forEach(accion => {
    contenedorDropdown1.addEventListener(accion, (e) => {
        e.preventDefault();
        e.stopPropagation();
    })
});

contenedorDropdown1.addEventListener('dragenter', () => {
    contenedorDropdown1.classList.add('border-red');
});
contenedorDropdown1.addEventListener('dragover', () => {
    contenedorDropdown1.classList.add('border-red');
});
contenedorDropdown1.addEventListener('dragleave', () => {
    contenedorDropdown1.classList.remove('border-red');
});
contenedorDropdown1.addEventListener('drop', () => {
    contenedorDropdown1.classList.remove('border-red');
});

function validarArchivoAlumnos(archivo){
    clearTimeout(timeoutAlumnos);
    if(archivo.name.endsWith('.xml') || archivo.type === 'text/xml' || archivo.type === 'application/xml'){
        mensajesDropdown1.innerHTML = `
            <img src="../img/xml.png" alt="Icono XML" width="50px" height="auto"><br>
            <span>${archivo.name}</span>
        `;
        mensajesDropdown1.classList.remove('text-danger');
        labelDropdown1.classList.add('d-none');
        inputFicheroAlumnos.disabled = true;
        contenedorDropdown1.parentElement.classList.add('flex-grow-1');
        divBorrarAlumnos.classList.remove('d-none');
        toggleGenerarActa();
    }else{
        mensajesDropdown1.textContent = 'Archivo no válido. Por favor, suba un archivo XML.';
        mensajesDropdown1.classList.add('text-danger');
        inputFicheroAlumnos.value = '';
        timeoutAlumnos = setTimeout(() => {
            mensajesDropdown1.textContent = '';
            mensajesDropdown1.classList.remove('text-danger');
        }, 5000);
    }
}

contenedorDropdown1.addEventListener('drop', (e) => {
    e.preventDefault();
    const archivos = e.dataTransfer.files;
    if(archivos.length == 1){
        inputFicheroAlumnos.files = archivos;
        validarArchivoAlumnos(archivos[0]);
    }else{
        mensajesDropdown1.textContent = 'Por favor, suba solo un archivo.';
        mensajesDropdown1.classList.add('text-danger');
        timeoutAlumnos = setTimeout(() => {
            mensajesDropdown1.textContent = '';
            mensajesDropdown1.classList.remove('text-danger');
        }, 5000);
    }
});

inputFicheroAlumnos.addEventListener('change', () => {
    if(inputFicheroAlumnos.files.length > 0){
        validarArchivoAlumnos(inputFicheroAlumnos.files[0]);
    }
});


// Función para hacer el drag and drop de los contenedores de los archivos profesores
const contenedorDropdown2 = document.getElementById('contenedorDropdown2');
const inputFicheroProfesores = document.getElementById('inputFicheroProfesores');
const mensajesDropdown2 = document.getElementById('mensajesDropdown2');
const labelDropdown2 = document.getElementById('labelDropdown2');
const divBorrarProfesores = document.getElementById('divBorrarProfesores');
let timeoutProfesores;

accionesNavegador.forEach(accion => {
    contenedorDropdown2.addEventListener(accion, (e) => {
        e.preventDefault();
        e.stopPropagation();
    })
});

contenedorDropdown2.addEventListener('dragenter', () => {
    contenedorDropdown2.classList.add('border-red');
});
contenedorDropdown2.addEventListener('dragover', () => {
    contenedorDropdown2.classList.add('border-red');
});
contenedorDropdown2.addEventListener('dragleave', () => {
    contenedorDropdown2.classList.remove('border-red');
});
contenedorDropdown2.addEventListener('drop', () => {
    contenedorDropdown2.classList.remove('border-red');
});


function validarArchivoProfesores(archivo){
    clearTimeout(timeoutProfesores);
    if(archivo.name.endsWith('.xlsx') || archivo.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
        mensajesDropdown2.innerHTML = `
            <img src="../img/xlsx.png" alt="Icono XLSX" width="50px" height="auto"><br>
            <span>${archivo.name}</span>
        `;
        mensajesDropdown2.classList.remove('text-danger');
        labelDropdown2.classList.add('d-none');
        inputFicheroProfesores.disabled = true;
        contenedorDropdown2.parentElement.classList.add('flex-grow-1');
        divBorrarProfesores.classList.remove('d-none');
        toggleGenerarActa();
    }else{
        mensajesDropdown2.textContent = 'Archivo no válido. Por favor, suba un archivo XLSX.';
        mensajesDropdown2.classList.add('text-danger');
        inputFicheroProfesores.value = '';
        timeoutProfesores = setTimeout(() => {
            mensajesDropdown2.textContent = '';
            mensajesDropdown2.classList.remove('text-danger');
        }, 5000);

        toggleGenerarActa();
    }
}

contenedorDropdown2.addEventListener('drop', (e) => {
    e.preventDefault();
    const archivos = e.dataTransfer.files;
    if(archivos.length == 1){
        inputFicheroProfesores.files = archivos;
        validarArchivoProfesores(archivos[0]);
    }else{
        mensajesDropdown2.textContent = 'Por favor, suba solo un archivo.';
        mensajesDropdown2.classList.add('text-danger');
        timeout = setTimeout(() => {
            mensajesDropdown2.textContent = '';
            mensajesDropdown2.classList.remove('text-danger');
        }, 5000);
    }
});

inputFicheroProfesores.addEventListener('change', () => {
    if(inputFicheroProfesores.files.length > 0){
        validarArchivoProfesores(inputFicheroProfesores.files[0]);
    }
});


// Funcionalidad botones para borrar los archivos seleccionados
const borrarAlumnos = document.getElementById('borrarEstudiantes');
borrarAlumnos.addEventListener('click', () => {
    inputFicheroAlumnos.value = '';
    mensajesDropdown1.textContent = '';
    labelDropdown1.classList.remove('d-none');
    inputFicheroAlumnos.disabled = false;
    contenedorDropdown1.parentElement.classList.remove('flex-grow-1');
    divBorrarAlumnos.classList.add('d-none');
    toggleGenerarActa();
});

const borrarProfesores = document.getElementById('borrarProfesores');
borrarProfesores.addEventListener('click', () => {
    inputFicheroProfesores.value = '';
    mensajesDropdown2.textContent = '';
    labelDropdown2.classList.remove('d-none');
    inputFicheroProfesores.disabled = false;
    contenedorDropdown2.parentElement.classList.remove('flex-grow-1');
    divBorrarProfesores.classList.add('d-none');
    toggleGenerarActa();
});


// Función para mostrar el botón de generar acta cuando se han subido ambos archivos
const divGenerarActa = document.getElementById('divGenerarActa');
function toggleGenerarActa(){
    if(inputFicheroAlumnos.disabled && inputFicheroProfesores.disabled) divGenerarActa.classList.remove('d-none');
    else divGenerarActa.classList.add('d-none');
}