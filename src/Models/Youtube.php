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
    
    public function downloadAndConvertVideo($videoId) {
        $videoId = $this->validateYouTubeVideoId($videoId);
        if (!$videoId) {
            echo "ID de video de YouTube inválido.";
            return;
        }
    
        $ytDlpCommand = escapeshellcmd("/var/multimarcas-dev/bin/yt-dlp --format bestaudio[ext=webm] https://www.youtube.com/watch?v=$videoId -o '".escapeshellarg("%(id)s.%(ext)s")."'");
        exec($ytDlpCommand, $outputYTDL, $returnYTDL);
    
        if ($returnYTDL === 0) {
            $webmFile = "$videoId.webm";  // Asume que se ha descargado correctamente
    
            if (file_exists($webmFile)) {
                $mp3File = preg_replace('/\.webm$/', '.mp3', $webmFile);
                $ffmpegConvertCommand = "/usr/bin/ffmpeg -i " . escapeshellarg($webmFile) . " -vn -ar 44100 -ac 2 -ab 192k " . escapeshellarg($mp3File);
                exec($ffmpegConvertCommand, $outputConvert, $returnConvert);
    
                if ($returnConvert === 0 && file_exists($mp3File)) {
                    // Limpiar cualquier buffer de salida previo
                    ob_end_clean();
    
                    // Configurar cabeceras para descarga de archivo
                    header('Content-Type: audio/mpeg');
                    header('Content-Disposition: attachment; filename="' . basename($mp3File) . '"');
                    header('Content-Length: ' . filesize($mp3File));
    
                    // Abrir el archivo en modo binario
                    $fp = fopen($mp3File, 'rb');
                    fpassthru($fp);
                    fclose($fp);
    
                    // Borrar los archivos después de enviarlos
                    unlink($mp3File);
                    unlink($webmFile);
    
                    exit;
                } else {
                    echo "Error en la conversión a MP3: " . implode("\n", $outputConvert);
                }
            } else {
                echo "Error: el archivo WEBM no fue encontrado.";
            }
        } else {
            echo "Error ejecutando yt-dlp o archivo no encontrado: " . implode("\n", $outputYTDL);
        }
    }
    
    

}