# API de Gestión del Menú

API para gestionar productos (hamburguesas y hotdogs) e ingredientes del menú.

## Productos

### Listar todos los productos

**Endpoint:** `GET /api/products`

**Descripción:** Retorna todos los productos disponibles con sus ingredientes por defecto.

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/products
```

**Ejemplo de respuesta:**
```json
{
  "products": [
    {
      "id": 1,
      "name": "Hamburguesa Clásica",
      "type": "hamburguesa",
      "price": "15.50",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z",
      "default_ingredients": [
        {
          "id": 1,
          "name": "Lechuga",
          "created_at": "2026-01-05T20:00:00.000000Z",
          "updated_at": "2026-01-05T20:00:00.000000Z"
        },
        {
          "id": 2,
          "name": "Tomate",
          "created_at": "2026-01-05T20:00:00.000000Z",
          "updated_at": "2026-01-05T20:00:00.000000Z"
        },
        {
          "id": 3,
          "name": "Cebolla",
          "created_at": "2026-01-05T20:00:00.000000Z",
          "updated_at": "2026-01-05T20:00:00.000000Z"
        }
      ]
    },
    {
      "id": 2,
      "name": "Hotdog Clásico",
      "type": "hotdog",
      "price": "12.00",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z",
      "default_ingredients": [
        {
          "id": 4,
          "name": "Mostaza",
          "created_at": "2026-01-05T20:00:00.000000Z",
          "updated_at": "2026-01-05T20:00:00.000000Z"
        },
        {
          "id": 5,
          "name": "Ketchup",
          "created_at": "2026-01-05T20:00:00.000000Z",
          "updated_at": "2026-01-05T20:00:00.000000Z"
        }
      ]
    }
  ]
}
```

### Ver un producto específico

**Endpoint:** `GET /api/products/{id}`

**Parámetros:**
- `id` (en la URL): ID del producto

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/products/1
```

**Ejemplo de respuesta:**
```json
{
  "product": {
    "id": 1,
    "name": "Hamburguesa Clásica",
    "type": "hamburguesa",
    "price": "15.50",
    "created_at": "2026-01-05T20:00:00.000000Z",
    "updated_at": "2026-01-05T20:00:00.000000Z",
    "default_ingredients": [
      {
        "id": 1,
        "name": "Lechuga"
      },
      {
        "id": 2,
        "name": "Tomate"
      }
    ]
  }
}
```

### Crear un nuevo producto

**Endpoint:** `POST /api/products`

**Body de la solicitud:**
```json
{
  "name": "Hamburguesa Especial",
  "type": "hamburguesa",
  "price": 18.50,
  "default_ingredients": [1, 2, 3, 4]
}
```

**Parámetros:**
- `name` (requerido): Nombre del producto
- `type` (requerido): Tipo de producto (`hamburguesa` o `hotdog`)
- `price` (requerido): Precio del producto (decimal)
- `default_ingredients` (opcional): Array de IDs de ingredientes por defecto

**Ejemplo de solicitud:**
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Hamburguesa Especial",
    "type": "hamburguesa",
    "price": 18.50,
    "default_ingredients": [1, 2, 3, 4]
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Producto creado exitosamente",
  "product": {
    "id": 3,
    "name": "Hamburguesa Especial",
    "type": "hamburguesa",
    "price": "18.50",
    "created_at": "2026-01-05T20:30:00.000000Z",
    "updated_at": "2026-01-05T20:30:00.000000Z",
    "default_ingredients": [
      {
        "id": 1,
        "name": "Lechuga"
      },
      {
        "id": 2,
        "name": "Tomate"
      },
      {
        "id": 3,
        "name": "Cebolla"
      },
      {
        "id": 4,
        "name": "Queso"
      }
    ]
  }
}
```

### Actualizar un producto

**Endpoint:** `PUT /api/products/{id}`

**Parámetros:**
- `id` (en la URL): ID del producto a actualizar

**Body de la solicitud:**
```json
{
  "name": "Hamburguesa Especial Premium",
  "type": "hamburguesa",
  "price": 20.00,
  "default_ingredients": [1, 2, 3, 4, 5]
}
```

**Parámetros (todos opcionales):**
- `name`: Nombre del producto
- `type`: Tipo de producto (`hamburguesa` o `hotdog`)
- `price`: Precio del producto
- `default_ingredients`: Array de IDs de ingredientes por defecto (reemplaza todos los existentes)

**Ejemplo de solicitud:**
```bash
curl -X PUT http://localhost:8000/api/products/3 \
  -H "Content-Type: application/json" \
  -d '{
    "price": 20.00
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Producto actualizado exitosamente",
  "product": {
    "id": 3,
    "name": "Hamburguesa Especial",
    "type": "hamburguesa",
    "price": "20.00",
    "default_ingredients": [...]
  }
}
```

### Actualizar ingredientes por defecto de un producto

**Endpoint:** `PUT /api/products/{id}/ingredients`

**Descripción:** Actualiza únicamente los ingredientes por defecto de un producto sin modificar otros campos.

**Parámetros:**
- `id` (en la URL): ID del producto

**Body de la solicitud:**
```json
{
  "ingredients": [1, 2, 3, 4, 5]
}
```

**Parámetros:**
- `ingredients` (requerido): Array de IDs de ingredientes por defecto

**Ejemplo de solicitud:**
```bash
curl -X PUT http://localhost:8000/api/products/1/ingredients \
  -H "Content-Type: application/json" \
  -d '{
    "ingredients": [1, 2, 3, 4, 5]
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Ingredientes por defecto actualizados exitosamente",
  "product": {
    "id": 1,
    "name": "Hamburguesa Clásica",
    "type": "hamburguesa",
    "price": "15.50",
    "default_ingredients": [
      {
        "id": 1,
        "name": "Lechuga"
      },
      {
        "id": 2,
        "name": "Tomate"
      },
      {
        "id": 3,
        "name": "Cebolla"
      },
      {
        "id": 4,
        "name": "Queso"
      },
      {
        "id": 5,
        "name": "Pepinillos"
      }
    ]
  }
}
```

### Eliminar un producto

**Endpoint:** `DELETE /api/products/{id}`

**Parámetros:**
- `id` (en la URL): ID del producto a eliminar

**Ejemplo de solicitud:**
```bash
curl -X DELETE http://localhost:8000/api/products/3
```

**Ejemplo de respuesta:**
```json
{
  "message": "Producto eliminado exitosamente"
}
```

## Ingredientes

### Listar todos los ingredientes

**Endpoint:** `GET /api/ingredients`

**Descripción:** Retorna todos los ingredientes disponibles.

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/ingredients
```

**Ejemplo de respuesta:**
```json
{
  "ingredients": [
    {
      "id": 1,
      "name": "Lechuga",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Tomate",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "Cebolla",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 4,
      "name": "Queso",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 5,
      "name": "Pepinillos",
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    }
  ]
}
```

### Ver un ingrediente específico

**Endpoint:** `GET /api/ingredients/{id}`

**Parámetros:**
- `id` (en la URL): ID del ingrediente

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/ingredients/1
```

**Ejemplo de respuesta:**
```json
{
  "ingredient": {
    "id": 1,
    "name": "Lechuga",
    "created_at": "2026-01-05T20:00:00.000000Z",
    "updated_at": "2026-01-05T20:00:00.000000Z"
  }
}
```

### Crear un nuevo ingrediente

**Endpoint:** `POST /api/ingredients`

**Body de la solicitud:**
```json
{
  "name": "Aguacate"
}
```

**Parámetros:**
- `name` (requerido): Nombre del ingrediente (debe ser único)

**Ejemplo de solicitud:**
```bash
curl -X POST http://localhost:8000/api/ingredients \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Aguacate"
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Ingrediente creado exitosamente",
  "ingredient": {
    "id": 6,
    "name": "Aguacate",
    "created_at": "2026-01-05T20:30:00.000000Z",
    "updated_at": "2026-01-05T20:30:00.000000Z"
  }
}
```

### Actualizar un ingrediente

**Endpoint:** `PUT /api/ingredients/{id}`

**Parámetros:**
- `id` (en la URL): ID del ingrediente a actualizar

**Body de la solicitud:**
```json
{
  "name": "Aguacate Premium"
}
```

**Parámetros:**
- `name` (requerido): Nuevo nombre del ingrediente (debe ser único)

**Ejemplo de solicitud:**
```bash
curl -X PUT http://localhost:8000/api/ingredients/6 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Aguacate Premium"
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Ingrediente actualizado exitosamente",
  "ingredient": {
    "id": 6,
    "name": "Aguacate Premium",
    "created_at": "2026-01-05T20:30:00.000000Z",
    "updated_at": "2026-01-05T20:35:00.000000Z"
  }
}
```

### Eliminar un ingrediente

**Endpoint:** `DELETE /api/ingredients/{id}`

**Parámetros:**
- `id` (en la URL): ID del ingrediente a eliminar

**Ejemplo de solicitud:**
```bash
curl -X DELETE http://localhost:8000/api/ingredients/6
```

**Ejemplo de respuesta:**
```json
{
  "message": "Ingrediente eliminado exitosamente"
}
```

## Flujo de Trabajo Recomendado

### Configurar el menú completo

1. **Crear ingredientes** primero:
   ```bash
   POST /api/ingredients
   Body: {"name": "Lechuga"}
   ```

2. **Crear productos** con sus ingredientes por defecto:
   ```bash
   POST /api/products
   Body: {
     "name": "Hamburguesa Clásica",
     "type": "hamburguesa",
     "price": 15.50,
     "default_ingredients": [1, 2, 3]
   }
   ```

3. **Actualizar ingredientes** de productos existentes si es necesario:
   ```bash
   PUT /api/products/{id}/ingredients
   Body: {"ingredients": [1, 2, 3, 4]}
   ```

### Consultar el menú

1. **Listar todos los productos** para ver el menú completo:
   ```bash
   GET /api/products
   ```

2. **Ver un producto específico** para conocer sus ingredientes:
   ```bash
   GET /api/products/{id}
   ```

3. **Listar todos los ingredientes** disponibles:
   ```bash
   GET /api/ingredients
   ```

## Tipos de Productos

| Tipo | Descripción |
|------|-------------|
| `hamburguesa` | Hamburguesa |
| `hotdog` | Hotdog |

## Notas Importantes

1. **Ingredientes por defecto**: Cada producto puede tener ingredientes por defecto que se incluyen automáticamente cuando se crea un pedido.

2. **Sistema de exclusión**: Los clientes pueden excluir ingredientes al hacer pedidos. Los ingredientes excluidos se especifican en el campo `excluded_ingredients` al crear un pedido.

3. **Unicidad de ingredientes**: El nombre de los ingredientes debe ser único en la base de datos.

4. **Relaciones**: 
   - Al eliminar un producto, se eliminan sus relaciones con ingredientes por defecto
   - Al eliminar un ingrediente, se eliminan sus relaciones con productos y pedidos

5. **Precios**: Los precios se almacenan como decimales con 2 decimales.

6. **Actualización de ingredientes**: 
   - Al actualizar un producto con `default_ingredients`, se reemplazan todos los ingredientes existentes
   - Usa `PUT /api/products/{id}/ingredients` para actualizar solo los ingredientes sin modificar otros campos



