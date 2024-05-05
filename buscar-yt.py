import sys
import json
from youtubesearchpython import VideosSearch

def search_youtube(query, limit=10):
    """Realiza una búsqueda en YouTube y devuelve los resultados en un diccionario."""
    try:
        videos_search = VideosSearch(query, limit=limit)
        return videos_search.result()
    except Exception as e:
        return {"error": str(e)}

def main():
    """Función principal que procesa los argumentos de la línea de comandos y muestra los resultados de la búsqueda."""
    if len(sys.argv) < 2:
        print("Uso: python busqueda.py 'término de búsqueda'")
        sys.exit(1)

    query = sys.argv[1]
    limit = int(sys.argv[2]) if len(sys.argv) > 2 else 10  # Permite especificar el límite de resultados como un segundo argumento opcional

    results = search_youtube(query, limit=limit)

    print(json.dumps(results, indent=4))

if __name__ == "__main__":
    main()
