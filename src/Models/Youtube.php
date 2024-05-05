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

    // Función para validar y limpiar el ID del video de YouTube
    private function validateYouTubeVideoId($videoId) {
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
            return $videoId;
        } else {
            return false; // Retorna false en lugar de lanzar una excepción
        }
    }
    
    // Función para descargar y convertir el video a MP3, luego enviar al cliente
    public function downloadAndConvertVideo($videoId) {
        $videoId = $this->validateYouTubeVideoId($videoId);
        if (!$videoId) {
            echo "ID de video de YouTube inválido.";
            return;
        }
    
        $ytDlpCommand = escapeshellcmd("/var/multimarcas-dev/bin/yt-dlp -g --format bestaudio[ext=webm] https://www.youtube.com/watch?v=$videoId");
        exec($ytDlpCommand, $outputYTDL, $returnYTDL);
    
        if ($returnYTDL === 0 && !empty($outputYTDL)) {
            $webmUrl = escapeshellarg($outputYTDL[0]);
            $webmFile = 'output.webm';
            $ffmpegDownloadCommand = "/usr/bin/ffmpeg -i $webmUrl -c copy $webmFile";
            exec($ffmpegDownloadCommand, $outputDownload, $returnDownload);
    
            if ($returnDownload === 0 && file_exists($webmFile)) {
                // Ahora convertir el archivo .webm a .mp3
                $mp3File = 'output.mp3';
                $ffmpegConvertCommand = "/usr/bin/ffmpeg -i $webmFile -vn -ar 44100 -ac 2 -ab 192k $mp3File";
                exec($ffmpegConvertCommand, $outputConvert, $returnConvert);
    
                if ($returnConvert === 0 && file_exists($mp3File)) {
                    // Configurar cabeceras para descarga de archivo
                    header('Content-Type: audio/mpeg');
                    header('Content-Disposition: attachment; filename="' . basename($mp3File) . '"');
                    header('Content-Length: ' . filesize($mp3File));
    
                    // Leer y enviar el archivo de forma eficiente
                    $fp = fopen($mp3File, 'rb');
                    fpassthru($fp);
                    fclose($fp);
    
                    // Borrar los archivos después de enviarlos
                    unlink($mp3File);
                    unlink($webmFile);
                } else {
                    echo "Error en la conversión a MP3: " . implode("\n", $outputConvert);
                }
            } else {
                echo "Error descargando el archivo WEBM: " . implode("\n", $outputDownload);
            }
        } else {
            echo "Error obteniendo la URL del video: " . implode("\n", $outputYTDL);
        }
    }
    
    
    

}