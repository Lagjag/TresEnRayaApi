### Anotaciones propias del proyecto

    - Proyecto creado por Luis Alamillo
    - Tecnologías:
        - Symfony
        - PHP 7.2
        - Doctrine 2

### Parte funcional

    El proyecto es una API REST que da servicio al frontal hecho en ReactJS.
    Este proyecto contiene toda la parte lógica del proyecto conjunto y 
    da servicio a la parte frontal hecha en ReactJS.

    Es un proyecto sencillo el cuál tiene una pequeña doc basada en swagger 
    gracias al paquete Nelmio. Además se ha colocado el CORS para que se pueda
    acceder a la api desde cualquier servidor (crossOrigin).

### Iniciando el proyecto

    Para iniciar el proyecto se deberá instalar con el composer lo necesario.

    Además se tendrán que cambiar distintas cosas de los siguientes archivos:
        - .env:
            - Cambiar los datos de la siguiente línea:
            DATABASE_URL=mysql://userdb:password@127.0.0.1:3306/tres_en_raya?serverVersion=5.7

                - mysql: nuestro servicio de base de datos
                - userdb: usuario de la bse de datos (tiene que tener permisos de creación)
                - password: contraseña de dicho usuario
                - 127.0.0.1:3306: donde tenemos el getor de base de datos montado
                - tres_en_raya: nombre de la base de datos (recomendado dejarlo así)
    
    Tras realizar esto arrancaremos el script "script_database.sh" con el que crearemos 
    la BD y en caso de que exista la destruirá y volverá a crear.

    Este proyecto se ha probado con el propio servidor de php, por ello el siguiente paso 
    sería arrancar el servidor con el siguiente comando:
        - "php bin/console server:start"
    
    Con ello veremos dónde se ha iniciado el servidor.

### Explicando parte interna

    Entidades:
        - Tablero: Esta será la única entidad del proyecto y controlará todo el desarrollo 
                    del tablero
            - id: Valor automatico para la BD
            - estado: Array del tablero con los movimientos.
            - turno: controla a qué jugador le pertenece el turno
            - modo_juego: controla el modo de juego IA o 2 jugadores

    Controladores:
        - ApiController: Desarrolla todos los Endpoints a los que llama la parte cliente.
            
            - Recuperar Tablero:
                - Función -> recuperarTablero()
                - Ruta -> /api/recuperar_tablero
                - Método -> GET
                - Recupera el último tablero jugado en BD
            
            - Crear Tablero:
                - Función -> crearTablero(Request)
                - Ruta -> /api/crear_tablero
                - Método -> POST
                - Crea un tablero nuevo en caso de que no haya ninguno en BD
            
            - Modificar Tablero:
                - Función -> modificarTablero(Request)
                - Ruta -> /api/modificar_tablero
                - Método -> PUT
                - Modifica el tablero y devuelve el nuevo al cliente

            - Grabar Tablero
                - Función -> grabarTablero(id, Request)
                - Ruta -> /api/grabar_tablero/{id}
                - Método -> PUT
                - Graba el tablero dependiendo el parámetro de grabado qeu requiere

    Adicionalmente hay información de estos métodos y ruta en la siguiente dirección:
        - /api/doc (gracias a nelmio/api-doc)

### Mejoras a largo plazo

    - Creación de usuarios
        - nombre
        - partidas ganadas

    - Records
        - idUsuario
        - partidas ganadas