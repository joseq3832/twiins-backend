# Formateo y Linting de Código

Este proyecto utiliza **Laravel Pint** para el formateo automático de código y **Captain Hook** como sistema de hooks de Git para automatizar el proceso de linting y formateo antes de cada commit.

## Herramientas Instaladas

### Laravel Pint
Laravel Pint es una herramienta de formateo de código opinionada para PHP que utiliza PHP-CS-Fixer bajo el capó.

### Captain Hook
Captain Hook es una alternativa a Husky para PHP que permite ejecutar scripts automáticamente en diferentes eventos de Git.

## Scripts de Composer Disponibles

### Formateo de Código
```bash
# Formatear todo el código automáticamente
./vendor/bin/sail composer format

# Verificar si el código está formateado correctamente (sin modificar archivos)
./vendor/bin/sail composer format-check
```

### Linting Completo
```bash
# Ejecutar verificación de formato + tests
./vendor/bin/sail composer lint

# Ejecutar el proceso completo de pre-commit manualmente
./vendor/bin/sail composer pre-commit
```

## Comandos Directos

### Laravel Pint
```bash
# Formatear todo el código
./vendor/bin/sail exec laravel.test ./vendor/bin/pint

# Verificar formato sin modificar archivos
./vendor/bin/sail exec laravel.test ./vendor/bin/pint --test

# Formatear archivos específicos
./vendor/bin/sail exec laravel.test ./vendor/bin/pint app/Models/

# Ver qué cambios se harían sin aplicarlos
./vendor/bin/sail exec laravel.test ./vendor/bin/pint --test -v
```

### Captain Hook
```bash
# Reinstalar hooks de Git
./vendor/bin/sail exec laravel.test ./vendor/bin/captainhook install

# Ejecutar hook de pre-commit manualmente
./vendor/bin/sail exec laravel.test ./vendor/bin/captainhook hook:pre-commit

# Verificar configuración
./vendor/bin/sail exec laravel.test ./vendor/bin/captainhook configure
```

## Configuración

### Laravel Pint
La configuración se encuentra en `pint.json` en la raíz del proyecto. Utiliza el preset de Laravel con reglas adicionales para:
- Ordenamiento de imports
- Eliminación de imports no utilizados
- Sintaxis de arrays corta
- Espaciado consistente
- Y muchas más reglas de formateo

### Captain Hook
La configuración se encuentra en `captainhook.json`. Los hooks configurados son:

#### Pre-commit
1. **Linting de PHP**: Verifica errores de sintaxis
2. **Laravel Pint**: Formatea automáticamente el código
3. **PHPUnit**: Ejecuta todos los tests

#### Commit-msg
- Valida el formato de los mensajes de commit

## Flujo de Trabajo

### Desarrollo Normal
1. Escribe tu código
2. Ejecuta `./vendor/bin/sail composer format` para formatear
3. Ejecuta `./vendor/bin/sail composer lint` para verificar
4. Haz commit (los hooks se ejecutarán automáticamente)

### Hooks Automáticos
Cuando hagas un commit, automáticamente se ejecutará:
1. Verificación de sintaxis PHP
2. Formateo automático con Laravel Pint
3. Ejecución de todos los tests
4. Validación del mensaje de commit

Si alguno de estos pasos falla, el commit será rechazado.

## Solución de Problemas

### Error de Permisos
Si encuentras errores de permisos, asegúrate de ejecutar los comandos dentro del contenedor:
```bash
./vendor/bin/sail exec laravel.test ./vendor/bin/pint
```

### Hooks No Funcionan
Si los hooks de Git no se ejecutan:
```bash
./vendor/bin/sail exec laravel.test ./vendor/bin/captainhook install --force
```

### Formateo Falla
Si el formateo falla, verifica la configuración en `pint.json` y ejecuta:
```bash
./vendor/bin/sail exec laravel.test ./vendor/bin/pint --test -v
```

## Personalización

### Modificar Reglas de Pint
Edita el archivo `pint.json` para añadir, quitar o modificar reglas de formateo.

### Modificar Hooks
Edita el archivo `captainhook.json` para cambiar qué acciones se ejecutan en cada hook.

### Añadir Nuevos Scripts
Añade nuevos scripts en la sección `scripts` del `composer.json`.