<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';


class Oferta extends Database
{
    private $documento;

    public function __construct($documentoOfertas)
    {
        $this->documento = $documentoOfertas;
    }

    public function extraerDatos()
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        
        $spreadsheet = $reader->load($this->documento);
        $sheet = $spreadsheet->getSheetByName('Digitar Aca Las Ofertas');

        $rows = [];
        $ofertaCount = 1;
        $columnNames = ['restriccion', 'descripcion', 'fecha_inicio', 'fecha_fin', 'ahorro', 'precio_venta', 'precio_oferta'];

        foreach ($sheet->getRowIterator() as $row) {
            // Si la fila es menor que 3, continua al siguiente ciclo del bucle
            if ($row->getRowIndex() < 3) {
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // This will include empty cells as well
            
            $rowValues = [];
            $colIndex = 0;
            $descripcionIsEmpty = false;

            foreach ($cellIterator as $cell) {
                $columnName = $columnNames[$colIndex] ?? $colIndex;
                $rowValues[$columnName] = $cell->getValue() ?? ''; // If the cell value is null, an empty string will be used
                
                if ($columnName === 'descripcion' && (is_null($cell->getValue()) || $cell->getValue() === '')) {
                    $descripcionIsEmpty = true;
                    break;
                }

                $colIndex++;
            }

            if ($descripcionIsEmpty) {
                break;
            }
            
            $rows["oferta_{$ofertaCount}"] = $rowValues;
            $ofertaCount++;
        }
        
        return $rows;
    }

    public function subirOfertas(){
        if ($_FILES["fileToUpload"]["size"] > 10485760){
            
        }
    }
}

$ruta = $_SERVER['DOCUMENT_ROOT'] . '/public/AFICHES DE OFERTAS_16 SEP .xlsx';
$oferta = new Oferta($ruta);

$ofertas = $oferta->extraerDatos();

foreach ($ofertas as $nombreOferta => $datosOferta) {
  echo "Procesando: {$nombreOferta}\n";

  // Ahora puedes acceder a cada detalle de la oferta
  echo "Restricción: " . $datosOferta['restriccion'] . "<br>";
  echo "Descripción: " . $datosOferta['descripcion'] . "<br>";
  echo "Fecha Inicio: " . $datosOferta['fecha_inicio'] . "<br>";
  echo "<hr>";
  // ... y así sucesivamente para los demás campos.

  // Aquí puedes hacer más procesamiento o manipulaciones según lo que necesites.
}
