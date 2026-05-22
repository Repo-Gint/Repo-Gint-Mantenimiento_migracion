# MantenimientoTestFrontend
Interfaz de usuario para el sistema de gestión de mantenimiento, desarrollada en Angular 21. Permite a los usuarios interactuar con órdenes de mantenimiento: crear, consultar, actualizar y recibir notificaciones de estado de manera sencilla y organizada.

# Objetivos
Proporcionar una interfaz moderna y responsiva.

Facilitar la gestión de órdenes de mantenimiento desde la Intranet.

Integrar notificaciones visuales y alertas para técnicos y usuarios.

Mejorar la experiencia de usuario con componentes reutilizables.

# 🏗️ Arquitectura
Framework: Angular 21

Estilos: Bootstrap 5 + Bootstrap Icons

Componentes adicionales: ngx-bootstrap, SweetAlert2

Comunicación: Angular Router y RxJS para manejo de estados y eventos.

# ⚙️ Tecnologías usadas
Angular CLI: ^21.2.11

Bootstrap: ^5.3.8

SweetAlert2: ^11.26.24

RxJS: ~7.8.0

# Requisitos Previos
Antes de ejecutar el proyecto, es necesario tener instalado lo siguiente en el equipo:

# 1. Instalar Node.js

Descargar e instalar Node.js desde el sitio oficial:

Node.js Oficial

Se recomienda utilizar una versión LTS.

Para verificar la instalación:
```bash
node -v
npm -v
```
# 2. Instalar Angular CLI
Instalar Angular CLI de manera global:
```bash
npm install -g @angular/cli
```
Verificar instalación:
```bash
ng version
```
# Clonar el Proyecto
```bash
1. git clone https://github.com/Repo-Gint/Repo-Gint-Mantenimiento_migracion.git
```
2. Entrar a la carpeta del frontend:
```bash
 cd mantenimiento_test_frontend
```
3. Instalar dependencias:
```bash
 npm install
```
4. Para iniciar un servidor de desarrollo local, ejecuta:
```bash
 ng serve
```
Una vez que el servidor esté en funcionamiento, abre tu navegador y navega a . La aplicación se recarga automáticamente cada vez que modifiques cualquiera de los archivos fuente.http://localhost:4200/

## Scripts Disponibles

| Comando | Descripción |
|----------|-------------|
| `npm start` | Inicia el servidor Angular |
| `ng serve` | Ejecuta el proyecto en modo desarrollo |
| `ng build` | Genera la compilación de producción |
| `ng test` | Ejecuta las pruebas unitarias |
| `ng build --watch` | Compila automáticamente al detectar cambios |

## 🤝 Contribución

1. Crear una rama de feature:
```bash
 git checkout -b feature/nueva-funcionalidad
```
2. Hacer commits siguiendo la convención:
```bash
 feat(frontend): agregar componente de órdenes
```
3. Crear un Pull Request hacia develop.

## Generación de Componentes
Angular CLI permite generar componentes y estructuras automáticamente.

Ejemplo para crear un componente:
```bash
 ng generate component nombre-componente
```
También se puede usar:
```bash
ng g c nombre-componente
```

## Notas Importantes
Verificar que el backend se encuentre ejecutándose correctamente.
Configurar correctamente las URLs de conexión a la API.
Mantener actualizadas las dependencias del proyecto.
Utilizar versiones compatibles de Node.js y Angular CLI.
