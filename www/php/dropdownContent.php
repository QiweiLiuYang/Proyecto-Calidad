<?php
    $html = '
        <div class="container-sm mt-5">
            <div class="row justify-content-center">
                <div class="col-2"></div>
                <div class="col-8 justify-content-center text-center">
                    <h2>Estudiantes</h2>
                    <div class="d-flex align-items-center">
                        <div class="p-5 border rounded-5 border-2 border-bg-pure-black mt-4 w-100">
                            <div id="contenedorDropdown1" class="p-2 border border-3 border-dashed">
                                <label id="labelDropdown1" class="row align-items-center" for="inputFicheroAlumnos">
                                    <div class="col-6">
                                        <span>Puede arrastrar y soltar aquí el archivo para añadirlo</span>
                                    </div>
                                    <div class="col-6 ps-5">
                                        <img src="../img/icono-subir-archivo.jpg" alt="Icono subir archivo" width="100px" height="auto">
                                    </div>
                                </label>
                                <div id="mensajesDropdown1" class="my-2"></div>
                                <input type="file" id="inputFicheroAlumnos" class="d-none" name="ficheroAlumnos" accept=".xml">
                            </div>
                        </div>
                        <div id="divBorrarAlumnos" class="d-none">
                            <button type="button" id="borrarEstudiantes" class=" btn ms-4 bg-transparent border rounded-3 border-bg-pure-black">
                                <img src="../img/papelera.png" alt="Icono papelera" width="40px" height="auto">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-2"></div>
                <div class="col-2"></div>
                <div class="col-8 justify-content-center text-center mt-5">
                    <h2>Profesores</h2>
                    <div class="d-flex align-items-center">
                        <div class="p-5 border rounded-5 border-2 border-bg-pure-black mt-4 w-100">
                            <div id="contenedorDropdown2" class="p-2 border border-3 border-dashed">
                                <label id="labelDropdown2" class="row align-items-center" for="inputFicheroProfesores">
                                    <div class="col-6">
                                        <span>Puede arrastrar y soltar aquí el archivo para añadirlo</span>
                                    </div>
                                    <div class="col-6 ps-5">
                                        <img src="../img/icono-subir-archivo.jpg" alt="Icono subir archivo" width="100px" height="auto">
                                    </div>
                                </label>
                                <div id="mensajesDropdown2" class="my-2"></div>
                                <input type="file" id="inputFicheroProfesores" class="d-none" name="ficheroProfesores" accept=".xlsx">
                            </div>
                        </div>
                        <div id="divBorrarProfesores" class="d-none">
                            <button type="button" id="borrarProfesores" class="btn ms-4 bg-transparent border rounded-3 border-bg-pure-black">
                                <img src="../img/papelera.png" alt="Icono papelera" width="40px" height="auto">
                            </button>
                        </div>
                    </div>
                    
                </div>
                <div class="col-2"></div>
                <div class="col-2"></div>
                <div id="divGenerarActa" class="col-8 text-center mt-5 d-none">
                    <button type="button" id="generarActa" class="btn btn-red border rounded-3 text-white px-5 py-2">Generar Acta</button>
                </div>
                <div class="col-2"></div>
                <div class="col-2"></div>
                <div class="col-8 justify-content-center text-center mt-5 d-none">
                    <h2>Actas</h2>
                    <div class="d-flex align-items-center">
                        <div class="p-5 border rounded-5 border-2 border-bg-pure-black mt-4 w-100">
                            <div id="contenedorActas" class="p-2">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
?>