class Product:
    def _init_(self, nombre, valor, desc, cant):
        self.nombre = nombre
        self.valor = valor
        self. desc = desc
        self.cant = cant

c = int(input("¿Cuántos productos ingresará?:"))

productos=[]

for i in range(c):
    print("Producto número:",i+1)
    n = input("Nombre del producto: ")
    vr = int(input("Valor del producto:"))
    desc = input("Descripción del producto")
    cant = int(input("Cantidad del producto"))
    p = Producto(n, vr, desc, cant)
    productos.append(p)

print("======LISTADO DE PRODUCTOS======")
for j in range(len(productos)):
    print("Nombre: ",productos[j].nombre)
    print("Descripción: ",productos[j].desc)
    print("Valor: ".productos[j].valor)
    print("Cantidad: ",productos[j].cant)
    print("============================")