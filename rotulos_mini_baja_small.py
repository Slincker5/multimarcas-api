import os
import requests
import sys
import openpyxl
import uuid
import json
from datetime import datetime


def hacer_peticion_con_token(url, token):
    # Configurar los encabezados de la solicitud con el token de autorizaciÃ³n
    headers = {
        'Authorization': f'Bearer {token}'
    }

    try:
        # Realizar la peticiÃ³n GET con los encabezados de autorizaciÃ³n
        response = requests.get(url, headers=headers)

        # Verificar el estado de la respuesta
        if response.status_code == 200:
            # Si la respuesta es exitosa (cÃ³digo 200), procesar la respuesta
            data = response.json()
            return data
        else:
            # Si la respuesta no es exitosa, imprimir el cÃ³digo de estado y el mensaje
            print(f'Error en la solicitud: {response.status_code} - {response.text}')
    except requests.exceptions.RequestException as e:
        # Capturar y manejar errores de conexiÃ³n u otras excepciones
        print(f'Error en la solicitud: {e}')


def editar_celdas_desde_json(archivo_excel, json_data, user_uuid):
    # Cargar el archivo de Excel
    workbook = openpyxl.load_workbook(archivo_excel)

    # Seleccionar la hoja "INSERTAR"
    hoja_insertar = workbook['DIGITAR OFERTAS']

    # Iterar sobre los elementos del JSON y editar las celdas correspondientes
    for i, item in enumerate(json_data, start=2):
        # Obtener los valores del elemento actual
        barra = item.get('barra')
        descripcion = item.get('descripcion')
        precio = item.get('precio')
        # ... y asÃ­ sucesivamente con los demÃ¡s campos

        # Modificar las celdas correspondientes en la hoja
        hoja_insertar['A' + str(i)].value = barra
        hoja_insertar['B' + str(i)].value = descripcion
        hoja_insertar['C' + str(i)].value = precio
        # ... y asÃ­ sucesivamente con las demÃ¡s celdas

    # Calcular las fÃ³rmulas en la hoja
    hoja_insertar.calculate_dimension()

    #ruta de guardado
    path_uuid = uuid.uuid4()

    ruta_directorio = os.path.join('public/afiches', user_uuid, str(path_uuid))
    os.makedirs(ruta_directorio, exist_ok=True)

    fecha_hora_actual = datetime.now()
    nombre_archivo = 'AFICHES-BAJA-DE-PRECIOS-' + fecha_hora_actual.strftime("%d-%m-%Y-%H%M%S") + '.xlsx'
    ruta_guardado = os.path.join(ruta_directorio, nombre_archivo)

    # Guardar los cambios en el archivo
    workbook.save(ruta_guardado)

    # Cerrar el archivo
    workbook.close()

    response = {
        'status': 'OK',
        'message': 'Archivo generado con exito',
        'path_name': nombre_archivo,
        'path_complete': ruta_guardado,
        'path_uuid': str(path_uuid),
        'user_uuid': user_uuid,
        'cantidad': len(json_data)
    }

    response_json = json.dumps(response)

    return response_json


if __name__ == "__main__":
    if len(sys.argv) < 4:  # Se debe pasar la URL, TOKEN y USER_UUID como argumentos
        print("Uso: python script.py URL TOKEN USER_UUID")
    else:
        url = sys.argv[1]
        token = sys.argv[2]
        user_uuid = sys.argv[3]

        # Realizar la peticiÃ³n con el token
        data = hacer_peticion_con_token(url, token)

        # Si la respuesta es vÃ¡lida, editar el archivo Excel
        if data:
            archivo_excel = 'public/documentos/BAJA_DE_PRECIO_SMALL.xlsx'  # Reemplazar con la ruta real del archivo Excel

            # Crear directorios dinÃ¡micamente
            
            resultado = editar_celdas_desde_json(archivo_excel, data, user_uuid)
            print(resultado)