<?php
namespace App\Models;

class Youtube {

    public function searchYouTube($searchQuery) {
        $command = escapeshellcmd("python3 buscar-yt.py '" . addslashes($searchQuery) . "'");
        $output = shell_exec($command);
        $arr = json_decode($output, true);
        return $arr;
    }

}