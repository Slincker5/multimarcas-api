<?php
namespace App\Models;

use App\Models\Database;

class Search extends Database {
  
  public function searching($txt){
    #$sql = 'SELECT * FROM codigos_global WHERE (interno LIKE ? OR barra LIKE ? OR descripcion LIKE ?)';
    $sql = "WITH GlobalMatches AS (SELECT cg.id, cg.interno, cg.barra, cg.descripcion, cg.factorEmpaque, cg.categoria, cg.genero, cg.coleccion, cg.marca, cg.existencia, cg.casa, c.precio, c.fecha, ROW_NUMBER() OVER(PARTITION BY cg.barra ORDER BY c.fecha DESC) AS rn FROM codigos_global cg LEFT JOIN codigos c ON cg.barra = c.barra WHERE cg.interno LIKE ? OR cg.barra LIKE ? OR cg.descripcion LIKE ?), CodigoMatches AS (SELECT c.id, c.interno, c.barra, c.descripcion, NULL as factorEmpaque, NULL as categoria, NULL as genero, NULL as coleccion, NULL as marca, NULL as existencia, NULL as casa, c.precio, c.fecha, ROW_NUMBER() OVER(PARTITION BY c.barra ORDER BY c.fecha DESC) AS rn FROM codigos c WHERE (c.barra LIKE ? OR c.descripcion LIKE ?) AND NOT EXISTS (SELECT 1 FROM GlobalMatches WHERE rn = 1)) SELECT * FROM GlobalMatches WHERE rn = 1 UNION ALL SELECT * FROM CodigoMatches WHERE rn = 1 AND NOT EXISTS (SELECT 1 FROM GlobalMatches WHERE rn = 1) ORDER BY fecha DESC;";



    $search_term = "%" . $txt . "%";
    $param = [$search_term, $search_term, $search_term, $search_term, $search_term];

    $search = $this->ejecutarConsulta($sql, $param);
    $response = $search->fetchAll(\PDO::FETCH_ASSOC);
    return $response;
}
}