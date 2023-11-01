#!/bin/bash

BASE_DIR="/var/www/multimarcas/public/cintillos"

# Procesar archivos .xlsx existentes
find "$BASE_DIR" -mindepth 2 -maxdepth 2 -type d | while read file_dir; do
    echo "Verificando directorio: $file_dir"

    if [[ ! -d "${file_dir}/tmp" ]]; then
        echo "Creando directorio tmp en: ${file_dir}/tmp"
        mkdir "${file_dir}/tmp"
    fi

    if [ -z "$(ls -A "${file_dir}/tmp")" ]; then
        for xlsx_file in "$file_dir"/*.xlsx; do
            if [[ -f "$xlsx_file" ]]; then
                echo "Convirtiendo archivo: $xlsx_file"
                libreoffice --headless --calc --convert-to xlsx "$xlsx_file" --outdir "${file_dir}/tmp"
            else
                echo "No se encontraron archivos .xlsx en $file_dir."
            fi
        done
    else
        echo "El directorio tmp en $file_dir no está vacío."
    fi
done

# Finalizar el script después de una única ejecución
exit 0
