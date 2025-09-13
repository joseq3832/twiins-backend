# Sistema de Filtrado Avanzado - Ejemplos de Uso

Este documento muestra ejemplos de cómo usar el sistema de filtrado avanzado implementado en el BaseRepository.

## Funcionalidades Disponibles

- ✅ Paginación
- ✅ Ordenamiento por múltiples columnas
- ✅ Búsqueda en columnas configuradas
- ✅ Selección de columnas específicas
- ✅ Filtrado con operadores avanzados
- ✅ Inclusión de relaciones
- ✅ Combinación de todas las funcionalidades

## Ejemplos de URLs

### 1. Paginación Básica
```
GET /api/employees?page=1&limit=10
```

### 2. Búsqueda
```
GET /api/employees?search=John
```
Busca "John" en las columnas configuradas como searchable (name, email, position)

### 3. Ordenamiento
```
# Ordenar por nombre ascendente
GET /api/employees?sort=name

# Ordenar por nombre descendente
GET /api/employees?sort=-name

# Ordenar por múltiples columnas
GET /api/employees?sort=position,-name
```

### 4. Selección de Columnas
```
GET /api/employees?select=id,name,email
```

### 5. Inclusión de Relaciones
```
GET /api/employees?include=immediateFamily
```

### 6. Filtros con Operadores

#### Igualdad ($eq)
```
GET /api/employees?filter[position][$eq]=Developer
```

#### No igual ($not)
```
GET /api/employees?filter[position][$not]=Manager
```

#### En lista ($in)
```
GET /api/employees?filter[position][$in]=Developer,Manager,Designer
```

#### Mayor que ($gt)
```
GET /api/employees?filter[hire_date][$gt]=2022-01-01
```

#### Mayor o igual que ($gte)
```
GET /api/employees?filter[hire_date][$gte]=2022-01-01
```

#### Menor que ($lt)
```
GET /api/employees?filter[hire_date][$lt]=2023-01-01
```

#### Menor o igual que ($lte)
```
GET /api/employees?filter[hire_date][$lte]=2023-01-01
```

#### Entre valores ($btw)
```
GET /api/employees?filter[hire_date][$btw]=2022-01-01,2023-12-31
```

#### Contiene texto ($ilike - case insensitive)
```
GET /api/employees?filter[name][$ilike]=john
```

#### Comienza con ($sw)
```
GET /api/employees?filter[name][$sw]=John
```

#### Contiene ($contains)
```
GET /api/employees?filter[email][$contains]=@company.com
```

#### Es nulo ($null)
```
GET /api/employees?filter[hire_date][$null]=true
GET /api/employees?filter[hire_date][$null]=false
```

### 7. Consultas Complejas Combinadas
```
GET /api/employees?
  search=Developer&
  filter[position][$eq]=Developer&
  filter[hire_date][$gte]=2022-01-01&
  sort=-name&
  select=id,name,position,hire_date&
  include=immediateFamily&
  page=1&
  limit=10
```

## Configuración en Repositorios

Para usar el sistema de filtrado, configura las columnas permitidas en el constructor del repositorio:

```php
class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
        
        // Configurar columnas para búsqueda
        $this->setSearchableColumns(['name', 'email', 'position']);
        
        // Configurar columnas para filtrado
        $this->setFilterableColumns(['name', 'email', 'position', 'hire_date']);
        
        // Configurar columnas para ordenamiento
        $this->setSortableColumns(['name', 'email', 'position', 'hire_date', 'created_at']);
        
        // Configurar columnas seleccionables
        $this->setSelectableColumns(['id', 'name', 'email', 'position', 'hire_date', 'created_at', 'updated_at']);
        
        // Configurar relaciones incluibles
        $this->setIncludableRelations(['immediateFamily']);
    }
}
```

## Respuesta JSON

La respuesta incluye metadatos de paginación:

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "position": "Developer",
      "hire_date": "2022-01-15",
      "immediate_family": [
        {
          "id": 1,
          "relative_name": "Jane Doe",
          "relationship": "spouse",
          "birth_date": "1990-05-20"
        }
      ]
    }
  ],
  "first_page_url": "http://localhost/api/employees?page=1",
  "from": 1,
  "last_page": 3,
  "last_page_url": "http://localhost/api/employees?page=3",
  "links": [...],
  "next_page_url": "http://localhost/api/employees?page=2",
  "path": "http://localhost/api/employees",
  "per_page": 10,
  "prev_page_url": null,
  "to": 10,
  "total": 25
}
```

## Operadores Disponibles

| Operador | Descripción | Ejemplo |
|----------|-------------|----------|
| `$eq` | Igual a | `filter[position][$eq]=Developer` |
| `$not` | No igual a | `filter[position][$not]=Manager` |
| `$null` | Es nulo/no nulo | `filter[hire_date][$null]=true` |
| `$in` | En lista | `filter[position][$in]=Dev,Manager` |
| `$gt` | Mayor que | `filter[hire_date][$gt]=2022-01-01` |
| `$gte` | Mayor o igual | `filter[hire_date][$gte]=2022-01-01` |
| `$lt` | Menor que | `filter[hire_date][$lt]=2023-01-01` |
| `$lte` | Menor o igual | `filter[hire_date][$lte]=2023-01-01` |
| `$btw` | Entre valores | `filter[hire_date][$btw]=2022-01-01,2023-12-31` |
| `$ilike` | Contiene (case insensitive) | `filter[name][$ilike]=john` |
| `$sw` | Comienza con | `filter[name][$sw]=John` |
| `$contains` | Contiene | `filter[email][$contains]=@company.com` |

## Notas Importantes

1. **Seguridad**: Solo las columnas configuradas en cada repositorio pueden ser filtradas, ordenadas o seleccionadas.
2. **Paginación**: Por defecto se usa página 1 con límite de 15 elementos.
3. **Ordenamiento**: Usa `-` como prefijo para orden descendente.
4. **Búsqueda**: Se aplica a todas las columnas configuradas como searchable.
5. **Relaciones**: Solo las relaciones configuradas pueden ser incluidas.