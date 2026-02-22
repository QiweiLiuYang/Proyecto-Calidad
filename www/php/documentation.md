# Iniciar contenido PHP y cargar librerías necesarias
Situarse en la carpeta php y ejecutar:
`composer install`

## Como parsear XLSX a un objeto en PHP
Utilizando la librería phpoffice/phpspreadsheet con el comando:

`composer require phpoffice/phpspreadsheet`

> Importante tener la extensión gd habilitada en php.ini
> extension=gd <--Correcto

Y en los ficheros php a utilizar, poner estas líneas:

`require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;`

