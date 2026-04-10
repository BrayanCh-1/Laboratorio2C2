# Laboratorio2C2

# Integrantes:
Brayan Audiel Chavarría Romero - SMSS020924
Esmeralda Isabel Cruz Roldán - SMSS011124

# Respuestas del Análisis

1. ¿De qué forma manejaste el login de usuarios? Explica con tus palabras porque en tu página funciona de esa forma.

En la página el login funciona con sesiones de PHP y una base de datos. El proceso es así:
 Cuando el usuario escribe su usuario y contraseña en `index.php`, esos datos se envían a `login.php`. Allí busca en la tabla `usuarios` si existe alguien con ese nombre de usuario. Si lo encuentra, usa `password_verify()` para comparar la contraseña que se escribió con la que está guardada (de forma encriptada) en la base de datos.

Si todo está bien, crea una sesión con `session_start()` y guarda el `id` y el nombre del usuario en variables de sesión como `$_SESSION['usuario_id']`. Luego lo mando al `dashboard.php`.

Ahora, en `dashboard.php` lo primero que se hace es preguntar: "¿Existe `$_SESSION['usuario_id']`?" Si no existe, significa que alguien intentó entrar sin loguearse, entonces lo redirige al `index.php`.

¿Por qué funciona así? Porque las sesiones son como una "credencial temporal" que el navegador guarda mientras el usuario está en la página. Sin sesiones, cada vez que el usuario haga clic en un enlace o recargue la página, el sistema "olvidaría" que ya inició sesión. Además, guardar las contraseñas encriptadas evita que si alguien roba la base de datos pueda ver las contraseñas reales.


2. ¿Por qué es necesario para las aplicaciones web utilizar bases de datos en lugar de variables?

Es necesario porque las variables solo viven durante una sola recarga de la página. Cuando el usuario termina de ver la página, todas las variables se borran y los datos se pierden para siempre.

Pongamos un ejemplo con el proyecto: Si se guardara los hábitos en una variable como `$mis_habitos = []`, cuando el usuario agregue un hábito y recargue la página, ese hábito desaparecería. También si cierra el navegador y vuelve a entrar, no vería nada de lo que había guardado.

Con la base de datos, los hábitos quedan guardados para siempre (o hasta que el usuario los borre). Además:
- Varios usuarios pueden usar la página al mismo tiempo sin problemas
- Los datos no se pierden si el servidor se apaga
- Se pueden buscar, ordenar y filtrar datos fácilmente

En este caso, se usa la base de datos para guardar usuarios y hábitos porque son datos que deben persistir entre sesiones.


3. ¿En qué casos sería mejor utilizar bases de datos para su solución y en cuáles utilizar otro tipo de datos temporales como cookies o sesiones?

Uso base de datos cuando:
- Los datos deben guardarse para siempre o por mucho tiempo. Ejemplo: Los hábitos que registra un usuario, su nombre de usuario, su contraseña.
- Varios usuarios necesitan ver la misma información. Ejemplo: Si fuera una red social, los posts de todos.
- Los datos son muchos y necesito buscarlos o filtrarlos rápido.

Uso sesiones o cookies cuando:
- Los datos son temporales y solo importan mientras el usuario está navegando. Ejemplo: En el proyecto se usa `$_SESSION['usuario_id']` solo para recordar que el usuario ya inició sesión mientras está en la página.
- Son preferencias no importantes, como si el usuario eligió "modo oscuro" o "modo claro" en la página.
- Son datos que no quiero guardar para siempre por seguridad. Ejemplo: El carrito de compras temporal antes de pagar.

En resumen: Lo que debe durar va a la base de datos. Lo que es solo para la visita actual va a sesiones o cookies.

4. Describa brevemente sus tablas y los tipos de datos utilizados en cada campo; justifique la elección del tipo de dato para cada uno.

Tengo dos tablas en mi base de datos `habitos_db`:

Tabla `usuarios` (guarda los usuarios registrados)

`id` | INT(11) | Es un número entero que aumenta automáticamente con cada usuario nuevo. Es la llave primaria. INT(11) es suficiente porque soporta hasta 2 mil millones de usuarios. |
`username` | VARCHAR(30) | Texto corto de hasta 30 caracteres. No necesito más porque un nombre de usuario típico es de 3 a 20 letras. VARCHAR ahorra espacio porque solo usa lo necesario. |
`password` | VARCHAR(60) | Guardo la contraseña encriptada con `password_hash()`. Esta función genera un texto de 60 caracteres siempre, así que con VARCHAR(60) es justo lo que necesito. |

Tabla `habitos` (guarda los hábitos que cada usuario registra)

`id` | INT(11) | Igual que en usuarios, es un número único para identificar cada hábito. |
`usuario_id` | INT(11) | Es el ID del usuario que creó este hábito. Con este campo sé qué hábito pertenece a qué usuario. Es una "llave foránea" que conecta con `usuarios(id)`. |
`habito` | VARCHAR(80) | Descripción del hábito, como "Hacer ejercicio" o "Leer 30 minutos". 80 caracteres son más que suficientes. |
`fecha` | DATE | Guarda solo la fecha (año-mes-día), sin la hora. Sirve para ordenar los hábitos y para validar que no se puedan poner fechas del futuro. |
`completado` | VARCHAR(10) | Guarda `"si"` o `"no"` para saber si el hábito se cumplió. Usé texto porque es más fácil de leer en el código que usar 0 o 1. |
`observaciones` | VARCHAR(200) | Es opcional (puede estar vacío). El usuario puede escribir una nota corta de hasta 200 caracteres. |

Relación entre tablas: Un usuario puede tener muchos hábitos, pero cada hábito pertenece a un solo usuario. Por eso la tabla `habitos` tiene `usuario_id` para apuntar al dueño del hábito. 


