package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"github.com/google/uuid"
	"github.com/xuri/excelize/v2"
)

type Respuesta struct {
	Barra       string `json:"barra"`
	Descripcion string `json:"descripcion"`
	Precio      string `json:"precio"`
}

func main() {
	if len(os.Args) != 4 {
		log.Fatalf("Uso: %s <URL> <TOKEN> <UUID>", os.Args[0])
	}

	url := os.Args[1]
	token := os.Args[2]
	userUUID := os.Args[3]

	client := &http.Client{}
	req, err := http.NewRequest("GET", url, bytes.NewBuffer(nil))
	if err != nil {
		log.Fatalf("Error creando la solicitud: %v", err)
	}
	req.Header.Set("Authorization", "Bearer "+token)

	resp, err := client.Do(req)
	if err != nil {
		log.Fatalf("Error realizando la solicitud: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		log.Fatalf("Error en la respuesta del servidor: %v", resp.Status)
	}

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatalf("Error leyendo la respuesta: %v", err)
	}

	var resultados []Respuesta
	if err := json.Unmarshal(body, &resultados); err != nil {
		log.Fatalf("Error al decodificar el JSON: %v", err)
	}
	cantidad := len(resultados)

	f, err := excelize.OpenFile("public/documentos/PLANTILLA_CINTILLOS.xlsx")
	if err != nil {
		log.Fatalf("Error al abrir el archivo: %v", err)
	}

	for i, resultado := range resultados {
		row := i + 3
		colPrefix := "B"
		if i >= 226 {
			row = (i - 226) + 3
			colPrefix = "E"
		}
		f.SetCellValue("DATA", fmt.Sprintf("%s%d", colPrefix, row), resultado.Barra)
		f.SetCellValue("DATA", fmt.Sprintf("%s%d", string(colPrefix[0]+1), row), resultado.Descripcion)
		f.SetCellValue("DATA", fmt.Sprintf("%s%d", string(colPrefix[0]+2), row), "$"+resultado.Precio)
	}

	parentDirPath := filepath.Join("public/cintillos", userUUID)
	err = os.MkdirAll(parentDirPath, 0755)
	if err != nil {
		log.Fatalf("Error al crear el directorio padre: %v", err)
	}

	pathUUID := uuid.New().String()
	childDirPath := filepath.Join(parentDirPath, pathUUID)
	err = os.MkdirAll(childDirPath, 0755)
	if err != nil {
		log.Fatalf("Error al crear el directorio hijo: %v", err)
	}

	now := time.Now().Format("2006-01-02-150405")
	fileName := fmt.Sprintf("CINTILLOS-%s.xlsx", now)
	filePath := filepath.Join(childDirPath, fileName)

	err = f.SaveAs(filePath)
	if err != nil {
		log.Fatalf("Error al guardar el archivo editado: %v", err)
	}

	// 1. Definir la ruta temporal y asegurarse de que exista
	tmpDir := filepath.Join(filepath.Dir(filePath), "tmp")
	err = os.MkdirAll(tmpDir, 0777)
	if err != nil {
		log.Fatalf("Error al crear el directorio temporal: %v", err)
	}
	tmpFilePath := filepath.Join(tmpDir, filepath.Base(filePath))

	response := map[string]interface{}{
		"status":        "OK",
		"message":       "Archivo generado con exito",
		"path_name":     fileName,
		"path_complete": filePath,
		"path_tmp":      tmpDir,
		"path_tmp_full": tmpFilePath,
		"path_uuid":     pathUUID,
		"user_uuid":     userUUID,
		"cantidad":      cantidad,
	}
	jsonResponse, err := json.Marshal(response)
	if err != nil {
		log.Fatalf("Error al generar el JSON de respuesta: %v", err)
	}

	fmt.Println(string(jsonResponse))
}
