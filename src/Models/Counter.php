<?php
namespace App\Models;

use App\Models\Database;

class Counter extends Database
{
    private $response;

    public function viewCounter($link_uuid, $origin, $device)
    {
        date_default_timezone_set('America/Mexico_City');
        $datenow = date('Y-m-d H:i:s');

        $sql = 'INSERT INTO clics (link_uuid, origin, device, date) VALUES (?, ?, ?, ?)';
        $clic = $this->ejecutarConsulta($sql, [$link_uuid, $origin, $device, $datenow]);

        if ($clic) {
            $response['status'] = 'OK';
            $response['message'] = 'view ok';
            return $response;
        }

    }

    public function validateLink($link_short)
    {
        $sql = 'SELECT link_uuid, link_real  FROM direcciones WHERE link_short = ?';
        $clic = $this->ejecutarConsulta($sql, [$link_short]);
        $data = $clic->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

}
