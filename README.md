## Mantenimiento_migracion

Sistema de gestión de mantenimiento que permite administrar órdenes mediante un inicio de sesión para administradores. El sistema recibe órdenes desde una plataforma externa llamada Intranet, permite crear, asignar y 
reasignar órdenes a técnicos, actualizar información y consultar mantenimientos finalizados. Además, envía notificaciones por correo electrónico a técnicos y usuarios sobre asignaciones, comentarios y actualizaciones
del proceso de mantenimiento, como retrasos o espera de materiales.

## Objetivo del Sistema

Desarrollar una plataforma centralizada para la gestión de órdenes de mantenimiento, permitiendo la administración, seguimiento y control de procesos operativos de manera más organizada y eficiente.

## Problemática que Resuelve

El sistema actual presenta limitaciones de escalabilidad, mantenimiento y organización tanto en la arquitectura como en la base de datos, además de utilizar tecnologías desactualizadas. La migración busca modernizar la
plataforma mediante una estructura más limpia, segura y escalable, mejorando la administración de órdenes, la experiencia del usuario y la facilidad para futuras mejoras.

## Arquitectura del Proyecto

El proyecto se encuentra dividido en tres capas principales:

- Frontend desarrollado en Angular
- Backend desarrollado en Laravel
- Base de datos MySQL

La comunicación entre frontend y backend se realiza mediante APIs REST.

## Tecnologías Utilizadas

### Frontend
- Angular
- TypeScript
- Bootstrap
- RxJS

### Backend
- Laravel
- PHP
- JWT Authentication

### Base de Datos
- MySQL

### Herramientas
- Git
- GitHub
- Postman
- Visual Studio Code

## Flujo General del Sistema

1. El usuario interactúa desde la interfaz web.
2. El frontend consume los servicios expuestos por la API.
3. El backend procesa la lógica de negocio.
4. La información es almacenada y consultada desde la base de datos.
5. Los resultados son retornados al frontend para su visualización.

## Instalación General

Para obtener una copia del proyecto en tu equipo local, ejecuta el siguiente comando en tu terminal:

```bash
git clone https://github.com/Repo-Gint/Repo-Gint-Mantenimiento_migracion.git

Una vez clonado el repositorio, podrás acceder a los módulos del sistema (frontend y backend) para continuar con sus respectivas configuraciones e instalaciones.

