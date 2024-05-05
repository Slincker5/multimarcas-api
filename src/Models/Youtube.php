<?php
namespace App\Models;

class Youtube
{
    // Busca en YouTube utilizando un script de Python
    public function searchYouTube($searchQuery)
    {
        $searchQuery = escapeshellarg($searchQuery); // Sanitiza la entrada para uso en shell
        $command = "/var/multimarcas-dev/bin/python3 buscar-yt.py $searchQuery";
        $output = shell_exec($command);
        $arr = json_decode($output, true);
        return $arr;
    }

    // Valida que el ID del video de YouTube tenga el formato correcto
    private function validateYouTubeVideoId($videoId)
    {
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
            return $videoId;
        } else {
            return false; // Devuelve falso si el ID es inválido
        }
    }

    


    public function downloadAndConvertVideo($videoId)
{
    $videoId = $this->validateYouTubeVideoId($videoId);
    if (!$videoId) {
        echo "Error: ID de video de YouTube inválido.";
        return;
    }

    $videoId = escapeshellarg($videoId); // Sanitiza el videoId para uso en shell
    $ytDlpCommand = "/var/multimarcas-dev/bin/yt-dlp -g --format bestaudio[ext=webm] https://www.youtube.com/watch?v=$videoId";

    exec($ytDlpCommand, $outputYTDL, $returnYTDL);

    if ($returnYTDL === 0 && !empty($outputYTDL)) {
        $url = $outputYTDL[0]; // Usamos la URL directamente
        $mp3File = 'output.mp3'; // Eliminamos escapeshellarg aquí
        $ffmpegCommand = "ffmpeg -i " . escapeshellarg($url) . " -vn -ar 44100 -ac 2 -ab 192k " . escapeshellarg($mp3File);

        exec($ffmpegCommand, $outputConvert, $returnConvert);

        if ($returnConvert === 0 && file_exists($mp3File)) {
            $this->sendFileToClient($mp3File);
        } else {
            echo "Error en la conversión: " . implode("\n", $outputConvert);
        }
    } else {
        echo "Error obteniendo la URL del video: " . implode("\n", $outputYTDL);
    }
}



    // Envía el archivo al cliente de manera segura
    private function sendFileToClient($mp3File)
    {
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: attachment; filename="' . basename($mp3File) . '"');
        header('Content-Length: ' . filesize($mp3File));

        $fp = fopen($mp3File, 'rb');
        fpassthru($fp);
        fclose($fp);

        unlink($mp3File);
    }
}
