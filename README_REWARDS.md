# API de Gestión de Recompensas

Sistema dinámico de recompensas basado en el número de pedidos completados por teléfono.

## Características

- **Recompensas configurables**: Crea, edita y elimina recompensas sin cambiar código
- **Reinicio automático**: Al alcanzar la última recompensa, el contador se reinicia automáticamente
- **Descuentos personalizables**: Cada recompensa puede tener un porcentaje de descuento diferente
- **Múltiples niveles**: Configura múltiples niveles de recompensas

## Cómo Funciona

1. Las recompensas se configuran en la tabla `reward_configs` con:
   - `order_count`: Número de pedidos necesarios para obtener la recompensa
   - `discount_percentage`: Porcentaje de descuento (0-100)
   - `order`: Orden de la recompensa (para identificar cuál es la última)
   - `is_active`: Si la recompensa está activa o no

2. El contador cuenta pedidos completados desde la última recompensa aplicada

3. Cuando un cliente alcanza una recompensa:
   - Se marca el pedido como `reward_applied = true`
   - El cliente obtiene el descuento en su próximo pedido

4. Al alcanzar la última recompensa (mayor `order`):
   - Se aplica la recompensa
   - El contador se reinicia automáticamente
   - El cliente puede comenzar un nuevo ciclo de recompensas

## Endpoints

### Listar todas las recompensas

**Endpoint:** `GET /api/rewards`

**Descripción:** Retorna todas las recompensas configuradas, ordenadas por `order`.

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/rewards
```

**Ejemplo de respuesta:**
```json
{
  "rewards": [
    {
      "id": 1,
      "order_count": 6,
      "discount_percentage": "10.00",
      "is_active": true,
      "order": 1,
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 2,
      "order_count": 7,
      "discount_percentage": "10.00",
      "is_active": true,
      "order": 2,
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    },
    {
      "id": 3,
      "order_count": 8,
      "discount_percentage": "10.00",
      "is_active": true,
      "order": 3,
      "created_at": "2026-01-05T20:00:00.000000Z",
      "updated_at": "2026-01-05T20:00:00.000000Z"
    }
  ]
}
```

### Crear una nueva recompensa

**Endpoint:** `POST /api/rewards`

**Body de la solicitud:**
```json
{
  "order_count": 5,
  "discount_percentage": 15.00,
  "is_active": true,
  "order": 1
}
```

**Parámetros:**
- `order_count` (requerido): Número de pedidos necesarios (entero, mínimo 1)
- `discount_percentage` (requerido): Porcentaje de descuento (decimal, 0-100)
- `is_active` (opcional): Si está activa (boolean, default: true)
- `order` (requerido): Orden de la recompensa (entero, mínimo 0). Usa números consecutivos (1, 2, 3...) para ordenar. El mayor número es la última recompensa.

**Ejemplo de solicitud:**
```bash
curl -X POST http://localhost:8000/api/rewards \
  -H "Content-Type: application/json" \
  -d '{
    "order_count": 5,
    "discount_percentage": 15.00,
    "is_active": true,
    "order": 1
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Recompensa creada exitosamente",
  "reward": {
    "id": 4,
    "order_count": 5,
    "discount_percentage": "15.00",
    "is_active": true,
    "order": 1,
    "created_at": "2026-01-05T20:30:00.000000Z",
    "updated_at": "2026-01-05T20:30:00.000000Z"
  }
}
```

### Ver una recompensa específica

**Endpoint:** `GET /api/rewards/{id}`

**Parámetros:**
- `id` (en la URL): ID de la recompensa

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/rewards/1
```

**Ejemplo de respuesta:**
```json
{
  "reward": {
    "id": 1,
    "order_count": 6,
    "discount_percentage": "10.00",
    "is_active": true,
    "order": 1,
    "created_at": "2026-01-05T20:00:00.000000Z",
    "updated_at": "2026-01-05T20:00:00.000000Z"
  }
}
```

### Actualizar una recompensa

**Endpoint:** `PUT /api/rewards/{id}`

**Parámetros:**
- `id` (en la URL): ID de la recompensa a actualizar

**Body de la solicitud:**
```json
{
  "order_count": 6,
  "discount_percentage": 15.00,
  "is_active": true,
  "order": 1
}
```

**Parámetros (todos opcionales):**
- `order_count`: Número de pedidos necesarios
- `discount_percentage`: Porcentaje de descuento
- `is_active`: Si está activa
- `order`: Orden de la recompensa

**Ejemplo de solicitud:**
```bash
curl -X PUT http://localhost:8000/api/rewards/1 \
  -H "Content-Type: application/json" \
  -d '{
    "discount_percentage": 15.00
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Recompensa actualizada exitosamente",
  "reward": {
    "id": 1,
    "order_count": 6,
    "discount_percentage": "15.00",
    "is_active": true,
    "order": 1,
    "created_at": "2026-01-05T20:00:00.000000Z",
    "updated_at": "2026-01-05T20:30:00.000000Z"
  }
}
```

### Eliminar una recompensa

**Endpoint:** `DELETE /api/rewards/{id}`

**Parámetros:**
- `id` (en la URL): ID de la recompensa a eliminar

**Ejemplo de solicitud:**
```bash
curl -X DELETE http://localhost:8000/api/rewards/1
```

**Ejemplo de respuesta:**
```json
{
  "message": "Recompensa eliminada exitosamente"
}
```

## Ejemplos de Configuración

### Configuración por defecto (6, 7, 8 pedidos con 10% de descuento)

```json
[
  {
    "order_count": 6,
    "discount_percentage": 10.00,
    "is_active": true,
    "order": 1
  },
  {
    "order_count": 7,
    "discount_percentage": 10.00,
    "is_active": true,
    "order": 2
  },
  {
    "order_count": 8,
    "discount_percentage": 10.00,
    "is_active": true,
    "order": 3
  }
]
```

### Configuración con descuentos progresivos

```json
[
  {
    "order_count": 3,
    "discount_percentage": 5.00,
    "is_active": true,
    "order": 1
  },
  {
    "order_count": 6,
    "discount_percentage": 10.00,
    "is_active": true,
    "order": 2
  },
  {
    "order_count": 10,
    "discount_percentage": 15.00,
    "is_active": true,
    "order": 3
  }
]
```

## Notas Importantes

1. **Orden de recompensas**: El campo `order` determina el orden de las recompensas. La recompensa con el mayor `order` es la última y reinicia el contador.

2. **Reinicio automático**: Cuando un cliente alcanza la última recompensa (mayor `order`), el contador se reinicia automáticamente y puede comenzar un nuevo ciclo.

3. **Recompensas activas**: Solo las recompensas con `is_active = true` se consideran para aplicar descuentos.

4. **Múltiples recompensas**: Un cliente puede tener múltiples recompensas activas si alcanza diferentes niveles. El descuento se aplica según el número actual de pedidos completados.

5. **Contador de pedidos**: El contador cuenta pedidos completados desde la última vez que se aplicó una recompensa (`reward_applied = true`).

## Flujo de Trabajo

1. **Configurar recompensas**: Crea las recompensas que deseas ofrecer usando `POST /api/rewards`

2. **Clientes completan pedidos**: Los clientes hacen pedidos con su teléfono

3. **Sistema verifica recompensas**: Cuando un pedido se completa, el sistema verifica si el cliente alcanzó alguna recompensa

4. **Aplicar recompensa**: Si alcanzó una recompensa, se marca el pedido y el cliente obtiene el descuento en su próximo pedido

5. **Reinicio automático**: Si alcanzó la última recompensa, el contador se reinicia automáticamente



