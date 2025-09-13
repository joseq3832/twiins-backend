# API Backend - Prueba Técnica Twiins

<p align="center">
  <strong>Sistema de gestión de empleados y familiares inmediatos</strong><br>
  Desarrollado con Laravel 11 + Laravel Sail + MySQL + JWT Authentication
</p>

---

## 📋 Descripción del Proyecto

Esta API REST permite gestionar empleados y sus familiares inmediatos, desarrollada como prueba técnica para **Twiins**. Incluye autenticación JWT, filtrado avanzado, documentación Swagger y testing completo.

### ✨ Características Principales

- 🔐 **Autenticación JWT** con refresh tokens
- 👥 **CRUD completo** de empleados y familiares
- 🔍 **Filtrado avanzado** con múltiples criterios
- 📚 **Documentación Swagger** interactiva
- 🐳 **Docker** con Laravel Sail
- 🧪 **Testing** con PHPUnit/Pest
- 🎯 **Arquitectura limpia** con repositorios

---

## 🚀 Instalación Rápida

### Prerrequisitos

- Docker Desktop instalado y ejecutándose
- Git
- Terminal/CMD

### 1. Clonar el Repositorio

```bash
git clone https://github.com/joseq3832/twiins-backend.git
cd twiins-backend
```

### 2. Ejecutar Script de Instalación

```bash
# Dar permisos de ejecución
chmod +x start.sh

# Ejecutar instalación automática
./start.sh
```

**¿Qué hace el script `start.sh`?**
- ✅ Configura el host local `backend.twiins.local`
- ✅ Copia `.env.example` a `.env`
- ✅ Levanta servicios Docker
- ✅ Genera clave de aplicación
- ✅ Ejecuta migraciones y seeders
- ✅ Genera documentación Swagger
- ✅ Limpia cachés

### 3. Acceder a la Aplicación

Una vez completada la instalación:

- **🌐 Aplicación:** http://backend.twiins.local
- **📚 Documentación API:** http://backend.twiins.local/api/documentation
- **📄 JSON para Postman:** http://backend.twiins.local/docs/api-docs.json

---

## 🔑 Credenciales de Prueba

```
Email: admin@admin.com
Password: admin
```

---

## 🐳 Uso de Laravel Sail

### Comandos Básicos

```bash
# Alias recomendado (ya configurado en start.sh)
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

# Levantar servicios
./vendor/bin/sail up -d

# Detener servicios
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Acceder al contenedor
./vendor/bin/sail shell

# Ejecutar comandos Artisan
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed

# Ejecutar tests
./vendor/bin/sail test

# Instalar dependencias
./vendor/bin/sail composer install
```

### Servicios Incluidos

- **Laravel App:** Puerto 80 (http://backend.twiins.local)
- **MySQL:** Puerto 3306
- **Redis:** Puerto 6379

---

## 📚 Documentación de la API

### Swagger UI

Accede a la documentación interactiva en:
**http://backend.twiins.local/api/documentation**

### Importar a Postman/Insomnia

1. **Obtener el archivo JSON:**
   - URL: http://backend.twiins.local/docs/api-docs.json
   - O descargar desde: `storage/api-docs/api-docs.json`

2. **En Postman:**
   - File → Import → Link
   - Pegar: `http://backend.twiins.local/docs/api-docs.json`
   - Click "Continue" → "Import"

3. **En Insomnia:**
   - Application → Preferences → Data → Import Data
   - Seleccionar "From URL"
   - Pegar: `http://backend.twiins.local/docs/api-docs.json`

### Endpoints Principales

```
POST   /api/auth/login          # Autenticación
POST   /api/auth/refresh        # Renovar token
POST   /api/auth/logout         # Cerrar sesión

GET    /api/employees           # Listar empleados (con filtros)
POST   /api/employees           # Crear empleado
GET    /api/employees/{id}      # Ver empleado
PUT    /api/employees/{id}      # Actualizar empleado
DELETE /api/employees/{id}      # Eliminar empleado

GET    /api/immediate-family    # Listar familiares
POST   /api/immediate-family    # Crear familiar
# ... más endpoints CRUD
```

---

## 🔧 Solución de Problemas

### Puerto 80 Ocupado

Si el puerto 80 está en uso:

1. **Liberar puerto 80:**
   ```bash
   # Encontrar proceso usando puerto 80
   sudo lsof -i :80
   
   # Detener Apache (si está corriendo)
   sudo brew services stop httpd
   # o
   sudo apachectl stop
   
   # Detener Nginx (si está corriendo)
   sudo brew services stop nginx
   ```

2. **O cambiar puerto de la aplicación:**
   ```bash
   # Editar .env
   APP_PORT=8080
   
   # Reiniciar servicios
   ./vendor/bin/sail down
   ./vendor/bin/sail up -d
   
   # Acceder en: http://backend.twiins.local:8080
   ```

### Problemas Comunes

**Error: "Address already in use"**
```bash
# Verificar puertos en uso
sudo lsof -i :80
sudo lsof -i :3306

# Cambiar puerto en .env si es necesario
APP_PORT=8080
```

**Error: "Permission denied" en start.sh**
```bash
chmod +x start.sh
```

**Error: "Host not found"**
```bash
# Verificar /etc/hosts
cat /etc/hosts | grep backend.twiins.local

# Si no existe, agregar manualmente:
echo "127.0.0.1 backend.twiins.local" | sudo tee -a /etc/hosts
```

**Limpiar y reiniciar todo:**
```bash
./vendor/bin/sail down -v  # Elimina volúmenes
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
./vendor/bin/sail test

# Tests específicos
./vendor/bin/sail test --filter AuthTest
./vendor/bin/sail test --filter EmployeeCrudTest
./vendor/bin/sail test --filter AdvancedFilteringTest

# Con coverage
./vendor/bin/sail test --coverage
```

### Tests Incluidos

- ✅ **AuthTest:** Autenticación JWT
- ✅ **EmployeeCrudTest:** CRUD de empleados
- ✅ **ImmediateFamilyCrudTest:** CRUD de familiares
- ✅ **AdvancedFilteringTest:** Filtros avanzados
- ✅ **HealthCheckTest:** Verificación de salud

---

## 🏗️ Arquitectura del Proyecto

### Estructura de Directorios

```
app/
├── Http/Controllers/     # Controladores API
├── Models/              # Modelos Eloquent
├── Repositories/        # Patrón Repository
├── Swagger/Schemas/     # Esquemas Swagger
└── Providers/           # Service Providers

database/
├── migrations/          # Migraciones de BD
├── seeders/            # Datos de prueba
└── factories/          # Factories para testing

tests/
├── Feature/            # Tests de integración
└── Unit/               # Tests unitarios
```

### Tecnologías Utilizadas

- **Framework:** Laravel 12
- **Base de Datos:** MySQL 8.0
- **Autenticación:** JWT (tymon/jwt-auth)
- **Documentación:** Swagger (darkaonline/l5-swagger)
- **Testing:** PHPUnit + Pest
- **Containerización:** Docker + Laravel Sail

---

## 📊 Información Técnica

### Modelos y Relaciones

- **User:** Usuario del sistema (autenticación)
- **Employee:** Empleado con datos personales y laborales
- **ImmediateFamily:** Familiares inmediatos del empleado
- **RefreshToken:** Tokens de renovación JWT

---

## 📞 Soporte

Para cualquier duda o problema durante la evaluación:

1. Revisar la sección de **Solución de Problemas**
2. Verificar logs: `./vendor/bin/sail logs`
3. Consultar documentación Swagger
4. Ejecutar tests para verificar funcionalidad
5. Contactarme al +593 97 892 7327

---

## 📄 Licencia

Este proyecto fue desarrollado como prueba técnica para **Twiins** y está basado en Laravel, que es software de código abierto licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  <strong>Desarrollado para Twiins - Prueba Técnica Backend</strong>
</p>
