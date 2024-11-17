
Tabla de ruteo:

| Recurso               | Verbo  | Controlador         | Metodo         |
|:----------------------|:------:|:-------------------:|:--------------:|
| libros                | GET    | LibroApiController  | obtenerLibros  |
| libros/:id            | GET    | LibroApiController  | obtenerLibro   |
| libros                | POST   | LibroApiController  | agregarLibro   |
| libros/:id            | DELETE | LibroApiController  | borrarLibros   |
| libros/:id            | PUT    | LibroApiController  | editarLibro    |
| libros/:id/en_oferta  | PUT    | LibroApiController  | editarLibro    |
|:----------------------|:------:|:-------------------:|:--------------:|
| generos                | GET    | GenerosApiController  | obtenerGeneros  |
| generos/:id            | GET    | GenerosApiController  | obtenerGenero   |
| generos                | POST   | GenerosApiController  | agregarGenero   |
| generos/:id            | DELETE | GenerosApiController  | borrarGenero    |
| generos/:id            | PUT    | GenerosApiController  | editarGenero    |
| generos/:id/           | PATCH  | GenerosApiController  | activarGenero   |

## Libros
### GET libros

> `| libros      | GET    | LibroApiController  | obtenerLibros  |`

A este método le podemos agregar los siguientes queryParams:

- `ordenarPor`: Se puede ordenar por cualquier campo de libros (menos por cover que es un campo opcional).
- `ofertas` : Para filtrar los libros que estén en oferta.
- `orden`: ascendente o descentiente

> y tambien está paginacion pero eso lo agrega más abajo

Ejemplo: `(GET) api/libros?ordenarPor=precio&orden=descendente`

> Ordenaria a los libros por orden de mayor a menor

### Get libros/:id

> | libros/:id  | GET    | LibroApiController  | obtenerLibro   |

Este es el servicio para obtener un sólo libro según su id

Ejemplo: `(GET) api/libros/6`

### DELETE libro/:id

>| libros/:id  | DELETE | LibroApiController  | borrarLibros   |

Este es el servicio para eliminar un libro según su ID.

Ejemplo: `(DELETE) api/libros/6`

### POST libro

Un ejemplo de una insercion: 

```json
{
  "titulo": "titulo",
  "autor": "autor", 
  "genero_id":3 , 
  "paginas": 90, 
  "cover": "",
  "precio": 7500.0
}
```

`(POST) api/libros`

Los parametros que se fijan que estén para valdiar los datos son *"titulo"*, *"genero_id"* y *"precio"*. 

> En `genero_id` también acepta el nombre de cualquiera de los generos que existan. Esto tuve que hacerlo así para poder mantener un **select** en el frontend y que no se rompa, pero es lo único para lo que se usa esta funcion, no sabia si dejarla o no. Cuando llega un nombre, la funcion pregunta si es un numero y si no lo es llama a una funcion que obtiene el id según el nombre.



### PUT libros/:id

> | libros/:id  | PUT    | LibroApiController  | editarLibro    |

Para editar un libro necesitamos todo el tener el libro que vamos a editar, osea el `id` y alterar los campos que querramos (obligatoriamente debe contener un nombre, id_genero y precio).

Ejemplo: `(PUT) api/libros/:id/`

```JSON
{
    "id_libro": 25,
    "titulo": "Libro Editado",
    "autor": "Este valor va a ser editado editado",
    "paginas": 1,
    "cover": "",
    "id_genero": 1,
    "en_oferta": 0,
    "precio": 1000
  }
```


### PUT libros/:id/en_oferta

> | libros/:id/en_oferta  | PUT    | LibroApiController  | editarLibro    |

Este es el servicio para cambiar el campo `en_oferta`. 

> Si ya está en oferta lo saca.

Ejemplo: `(PUT) api/libros/:id/en_oferta`

> No se le envian datos

### Paginacion:

Para realizar una paginacion de los libros agregue los siguientes queryParams:

- pagina
- limite

Que se utilizan sobre el get de libros normal. Ejemplo:

`(GET) api/libros?pagina=2&limite=5`

Si hubiesen 10 libros traeria los ultimos 5.

Si limite no es definido toma por defecto el valor 10. Si pagina no es definido va a funcionar como funcionaria normalmente sin los parametros.

Esta es la funcion que hace la páginacion:

```php
if(isset($pagina) && $pagina!=false){
  if(!isset($limite) || $limite == false)
     $limite = 10;   
  if (count($libros) > $limite) {
      //    13          10
      $final =  $limite * $pagina;//10 * 2
      $inicio = $final - $limite  // 20 - 10 
      if($final > count($libros)) // Si (20 > 13)
          $final = count($libros);// Final se transforma en la cantidad de libros*
      for($i = $inicio; $i < $final; $i++){
          array_push($librosLimitados, $libros[$i]);
      }   
  }
}
```

>  *: Esto seria en caso de ser la **última página**. Por ejemplo: si me piden la pagina 2 con limite de 10 y  hay 13 libros.
>
> Las varibales quedarian
> 
> $inicio = 10;
> 
> $final = 20;
> 
> count($libros) = 13;
> 
> En este caso me pasaria de largo, por eso hago este control, porque ya sé que estoy en el elemento 10 y entonces solamente voy a hasta el final de los libros que tengo, en este caso 13.


## Generos
### GET Generos

> `| generos      | GET    | GenerosApiController  | obtenerGeneros  |`

Este endpoint permite obtener una lista de géneros, con opciones adicionales para filtrar y ordenar. Se pueden utilizar los siguientes **queryParams**:

- **`ordenarPor`**: Permite ordenar por cualquier campo de la tabla `generos` (por ejemplo, `nombre`, `id`, etc.).
- **`orden`**: Define el orden del listado, que puede ser `ascendente` o `descendente`.

#### Ejemplo de llamada:
```http
GET api/generos?ordenarPor=nombre&orden=ascendente
```

#### Código:
El código para implementar este comportamiento sería similar al siguiente, asegurando la validación de los campos para evitar inyecciones SQL:

```php
if ($ordenarPor) {
            switch ($ordenarPor) {
                case 'nombre':
                    $sql .= " ORDER BY nombre";
                    break;
                case 'descripcion':
                    $sql .= " ORDER BY descripcion";
                    break;
                case 'activo':
                    $sql .= " ORDER BY activo";
                    break;
                case 'fecha_actualizacion':
                    $sql .= " ORDER BY fecha_actualizacion";
                    break;
            }
        }
        if($orden && $ordenarPor != false){
            switch ($orden) {
                case 'ascendente':
                    $sql .= " ASC";
                    break;
                
                case 'descendente':
                    $sql .= " DESC";
                    break;
                
                default:
                    # code...
                    break;
            }
```

### **GET generos/:id**

> `| generos/:id  | GET    | GenerosApiController  | obtenerGenero   |`

Permite obtener un género específico basado en su ID. Este servicio se utiliza principalmente para recuperar los detalles de un género.

#### Ejemplo de llamada:
```http
GET api/generos/2
```

### **POST generos**

Permite agregar un nuevo género. El cuerpo de la solicitud debe incluir los datos mínimos requeridos, como el nombre del género y la descripcion del genero. (Los demas campos se configuran automaticamente en activo y fecha de actualizacion toma la fecha actual)

#### Ejemplo de carga:
```json
{
  "nombre": "Ciencia Ficción",
  "descripcion": "Un género dedicado a la ficción científica."
}
```

#### Validaciones:
- **`nombre`**: Campo obligatorio, único.
- **`descripcion`**: Campo obligatorio.

### **DELETE generos/:id**

> `| generos/:id  | DELETE | GenerosApiController  | borrarGenero   |`

Permite eliminar un género basado en su ID. Este servicio es útil para mantener la lista de géneros actualizada y libre de elementos obsoletos.

#### Ejemplo de llamada:
```http
DELETE api/generos/3
```

### **PUT generos/:id**

> `| generos/:id  | PUT    | GenerosApiController  | editarGenero   |`

Permite editar un género existente, modificando sus campos según las necesidades.

#### Ejemplo de datos:
```json
{
  "nombre": "Fantástico",
  "descripcion": "Un género centrado en lo fantástico.",
t
}
```

#### Validaciones:
- **`nombre`**: Debe ser único si se edita.
- **`activo`**: Solo acepta valores `true` o `false`.

### **PATCH generos/:id**

> `| generos/:id  | PATCH  | GenerosApiController  | activarGenero   |`

Este servicio es específico para activar o desactivar un género. Solo requiere un ID en la URL.

#### Ejemplo de llamada:
```http
PATCH api/generos/3
```

### **Paginación para Generos**

La paginación en el endpoint de géneros sigue el mismo esquema que en libros:

- **`pagina`**: Define el número de página a obtener.
- **`limite`**: Especifica cuántos géneros por página se deben mostrar. Por defecto es `10`.
Si la pagina o el limite son invalidos devuelve todo el conjunto de datos.
#### Ejemplo de llamada:
```http
GET api/generos?pagina=2&limite=5
```

#### Implementación:
El código para manejar la paginación sería idéntico al utilizado en `libros`, asegurándote de trabajar sobre la colección adecuada.


