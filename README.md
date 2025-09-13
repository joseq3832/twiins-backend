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

GET    /api/health             # Health Check (monitoreo)
```

---

## 🏥 Módulo Health Check - Monitoreo y CI/CD

### ¿Por qué es importante el Health Check?

El endpoint `/api/health` es **fundamental** para los procesos de **CI/CD** y **monitoreo en producción**. Proporciona una verificación integral del estado de la aplicación y sus dependencias.

### 🎯 Beneficios en CI/CD

#### 1. **Verificación Post-Deployment**
```bash
# En pipelines de CI/CD
curl -f http://backend.twiins.local/api/health || exit 1
```
- ✅ Confirma que el deployment fue exitoso
- ✅ Valida conectividad con servicios externos
- ✅ Detecta problemas antes que los usuarios

#### 2. **Smoke Tests Automatizados**
```yaml
# Ejemplo GitHub Actions
- name: Health Check
  run: |
    response=$(curl -s -o /dev/null -w "%{http_code}" http://app/api/health)
    if [ $response != "200" ]; then
      echo "Health check failed with status $response"
      exit 1
    fi
```

#### 3. **Rolling Deployments**
- **Blue-Green Deployments:** Verificar nueva versión antes del switch
- **Canary Releases:** Monitorear salud durante rollout gradual
- **Zero-Downtime:** Asegurar que servicios estén listos antes de recibir tráfico

### 🔍 Qué Verifica el Health Check

#### **Base de Datos**
```json
{
  "database": {
    "status": "healthy",
    "message": "Database connection successful",
    "response_time_ms": 12.5,
    "connection": "mysql"
  }
}
```

#### **Sistema de Cache**
```json
{
  "cache": {
    "status": "healthy",
    "message": "Cache system operational",
    "response_time_ms": 3.2,
    "driver": "redis"
  }
}
```

#### **Sistema de Almacenamiento**
```json
{
  "storage": {
    "status": "healthy",
    "message": "Storage system operational",
    "response_time_ms": 8.1,
    "driver": "local"
  }
}
```

#### **Estado de la Aplicación**
```json
{
  "application": {
    "status": "healthy",
    "message": "Application running normally",
    "php_version": "8.4.0",
    "laravel_version": "11.x"
  }
}
```

### 🚨 Estados de Respuesta

| Estado | HTTP Code | Descripción |
|--------|-----------|-------------|
| `healthy` | 200 | Todos los servicios funcionan correctamente |
| `degraded` | 200 | Servicios operativos pero con advertencias |
| `unhealthy` | 503 | Uno o más servicios críticos fallan |

### 🔧 Integración con Herramientas de Monitoreo

#### **Kubernetes Probes**
```yaml
livenessProbe:
  httpGet:
    path: /api/health
    port: 80
  initialDelaySeconds: 30
  periodSeconds: 10

readinessProbe:
  httpGet:
    path: /api/health
    port: 80
  initialDelaySeconds: 5
  periodSeconds: 5
```

#### **Docker Compose Healthcheck**
```yaml
services:
  app:
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/api/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
```

#### **Load Balancer Health Checks**
- **AWS ALB/ELB:** Configurar health check en `/api/health`
- **Nginx:** Usar como upstream health check
- **HAProxy:** Verificación de backend servers

### 📊 Métricas y Alertas

#### **Prometheus/Grafana**
```bash
# Métricas expuestas
http_requests_total{endpoint="/api/health",status="200"}
health_check_response_time_seconds
health_check_database_status
health_check_cache_status
```

#### **Alertas Recomendadas**
```yaml
# Ejemplo Prometheus Alert
- alert: HealthCheckFailing
  expr: health_check_status != 1
  for: 2m
  labels:
    severity: critical
  annotations:
    summary: "Application health check failing"
```

### 🛡️ Seguridad y Rate Limiting

- **Throttling:** 60 requests/minuto para prevenir abuso
- **Sin autenticación:** Accesible para sistemas de monitoreo
- **Información limitada:** No expone datos sensibles en producción

### 🧪 Testing del Health Check

```bash
# Ejecutar tests específicos
./vendor/bin/sail test tests/Feature/HealthCheckTest.php

# Verificar manualmente
curl -i http://backend.twiins.local/api/health
```

**El módulo Health Check es esencial para:**
- ✅ **Deployments seguros** y verificables
- ✅ **Monitoreo proactivo** de la aplicación
- ✅ **Detección temprana** de problemas
- ✅ **Automatización** de procesos DevOps
- ✅ **Cumplimiento** de SLAs y SLOs

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
