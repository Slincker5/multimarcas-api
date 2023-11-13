import os
import requests
import sys
import openpyxl
import uuid
import json
from datetime import datetime

def hacer_peticion_con_token(url, token):
    # Configurar los encabezados de la solicitud con el token de autorización
    headers = {
        'Authorization': f'Bearer {token}'
    }

    try:
        # Realizar la peticion GET con los encabezados de autorización
        response = requests.get(url, headers=headers)

        # Verificar el estado de la respuesta
        if response.status_code == 200:
            # Si la respuesta es exitosa (código 200), procesar la respuesta
            data = response.json()
            return data
        else:
            # Si la respuesta no es exitosa, imprimir el código de estado y el mensaje
            print(f'Error en la solicitud: {response.status_code} - {response.text}')
            return None
    except requests.exceptions.RequestException as e:
        # Capturar y manejar errores de conexión u otras excepciones
        print(f'Error en la solicitud: {e}')
        return None


def editar_celdas_desde_json(archivo_excel, json_data, user_uuid):
    # Cargar el archivo de Excel
    workbook = openpyxl.load_workbook(archivo_excel)

    # Seleccionar la hoja "INSERTAR"
    hoja_insertar = workbook['INSERTAR']

    # Iterar sobre los elementos del JSON y editar las celdas correspondientes
    for i, item in enumerate(json_data, start=3):
        # Obtener los valores del elemento actual
        barra = item.get('barra')
        descripcion = item.get('descripcion')
        f_inicio = item.get('f_inicio')
        f_fin = item.get('f_fin')
        precio = item.get('precio')
        # ... y así sucesivamente con los demás campos

        # Modificar las celdas correspondientes en la hoja
        hoja_insertar['A' + str(i)].value = barra
        hoja_insertar['B' + str(i)].value = descripcion
        hoja_insertar['C' + str(i)].value = f_inicio
        hoja_insertar['D' + str(i)].value = f_fin
        hoja_insertar['E' + str(i)].value = precio
        # ... y así sucesivamente con las demás celdas

    # Calcular las fórmulas en la hoja
    hoja_insertar.calculate_dimension()

    # Ruta de guardado
    path_uuid = uuid.uuid4()
    ruta_directorio = os.path.join('public/afiches', user_uuid, str(path_uuid))
    os.makedirs(ruta_directorio, exist_ok=True)

    fecha_hora_actual = datetime.now()
    nombre_archivo = 'AFICHES-' + fecha_hora_actual.strftime("%d-%m-%Y-%H%M%S") + '.xlsx'
    ruta_guardado = os.path.join(ruta_directorio, nombre_archivo)

    # Guardar los cambios en el archivo
    workbook.save(ruta_guardado)

    # Cerrar el archivo
    workbook.close()

    # Crear la respuesta
    response = {
        'status': 'OK',
        'message': 'Archivo generado con éxito',
        'path_name': nombre_archivo,
        'path_complete': ruta_guardado,
        'path_uuid': str(path_uuid),
        'user_uuid': user_uuid,
        'cantidad': len(json_data)  # Agregar la cantidad de elementos en el array
    }

    response_json = json.dumps(response)

    return response_json


if __name__ == "__main__":
    if len(sys.argv) < 4:  # Se debe pasar la URL, TOKEN y USER_UUID como argumentos
        print("Uso: python script.py URL TOKEN USER_UUID")
        sys.exit(1)

    url = sys.argv[1]
    token = sys.argv[2]
    user_uuid = sys.argv[3]

    # Realizar la petición con el token
    data = hacer_peticion_con_token(url, token)

    # Si la respuesta es válida, editar el archivo Excel
    if data:
        archivo_excel = 'public/documentos/PLANTILLA_INTERNAS.xlsx'  # Reemplazar con la ruta real del archivo Excel
        resultado = editar_celdas_desde_json(archivo_excel, data, user_uuid)
        print(resultado)
