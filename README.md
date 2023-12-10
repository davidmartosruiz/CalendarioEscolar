# CalendarioEscolar
Repositorio para la aplicación web Calendario Escolar, proyecto en PHP para el módulo profesional Desarrollo web en entorno servidor.

Desarrollado por **David Martos Ruiz** 2º DAW Mañanas.

Software distribuido bajo licencia GNU GPL v3.0.

Para mí, el software libre representa la libertad de poder crear, modificar y distribuir software sin ningún tipo de restricción, y que además, este sea accesible para todo el mundo. El software libre es una forma de compartir conocimiento y de aprender de los demás, y por eso, creo que es una de las mejores formas de aprender a programar y contribuir al resto de compañeros.

## Instalación
Para instalar la aplicación, es necesario tener instalado un servidor web con PHP y MySQL.

El único fichero que hay que modificar es .env, donde se encuentran las variables de entorno para la conexión a la base de datos y el correo electrónico.

Es necesario contar con una BBDD MySQL/MariaBD, así como una conexión a un servidor SMTP para el envío de correos electrónicos.

## Uso
La aplicación está pensada para ser utilizada por los alumnos de un centro educativo, para que puedan consultar las fechas de exámenes, entregas de trabajos, etc.

Para acceder a la aplicación, no es necesario registrarse con un correo electrónico y una contraseña. Simplemente, cualquier alumno puede acceder a la aplicación y consultar las fechas de los exámenes.

El profesorado, sin embargo, sí que debe registrarse con un correo electrónico y una contraseña. Una vez registrado, el profesor puede crear asignaturas, y en cada asignatura, crear eventos.

El alumnado podrá darse de alta en la newsletter, para recibir un correo electrónico cada vez que se cree, modifique y elimine un evento.

## Despliegue de la BBDD
Para desplegar la BBDD, es necesario ejecutar el script SQL que se encuentra en la carpeta `database`, llamado instalacion.sql.

Este script creará la base de datos y las tablas necesarias para el funcionamiento de la aplicación, así como creará un usuario administrador con el que se podrá acceder a la aplicación, para posteriormente crear más usuarios.

El usuario administrador creado es el siguiente:
Correo electrónico: usuario@example.com
Contraseña: usuario

## Despliegue de la aplicación
Para desplegar la aplicación, es necesario copiar todos los ficheros en la carpeta del servidor web, y configurar el fichero .env con los datos de la base de datos y del servidor SMTP.

Será necesario que el servidor web PHP tenga instalado Composer, junto con las dependencias de la aplicación: PHPMailer, Dotenv, Twig y Symfony.

Actualmente, la aplicación está desplegada en un servidor de AWS, y se puede acceder a ella a través de la siguiente URL: https://calendarioescolar.duckdns.org

## Excepción de responsabilidad
La aplicación ha sido desarrollada para un proyecto de clase, y no está pensada necesariamente para ser utilizada en un entorno de producción. Por tanto, no me hago responsable de cualquier daño que pueda causar la aplicación, ni de la pérdida de datos que pueda ocasionar.

Pese a ello, he intentado que la aplicación sea lo más segura posible, y que no haya ningún tipo de vulnerabilidad que pueda ser explotada por un atacante.

Si se encuentra algún tipo de vulnerabilidad, por favor, contacte conmigo para que pueda solucionarlo lo antes posible.