# API de Gestión de Pedidos

API para gestionar pedidos de hamburguesas y hotdogs con ingredientes personalizados, información del cliente, método de pago, seguimiento de tiempos y sistema de recompensas.

## Visualizar Pedidos

### Obtener todos los pedidos (ordenados del más antiguo al más reciente)

**Endpoint:** `GET /api/orders`

**Descripción:** Retorna todos los pedidos ordenados del más antiguo al más reciente, permitiendo procesar las órdenes en el orden correcto.

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/orders
```

**Ejemplo de respuesta:**
```json
{
  "orders": [
    {
      "id": 1,
      "status": "pending",
      "payment_method": "efectivo",
      "phone": "+1234567890",
      "customer_name": "Juan Pérez",
      "address": "Calle Principal 123, Ciudad",
      "delivery_type": "domicilio",
      "created_at": "2026-01-05T19:00:00.000000Z",
      "updated_at": "2026-01-05T19:00:00.000000Z",
      "started_at": null,
      "completed_at": null,
      "time_since_received": 120,
      "time_since_received_formatted": "2:00",
      "preparation_time": null,
      "preparation_time_formatted": null,
      "total_time": 120,
      "total_time_formatted": "2:00",
      "items": [
        {
          "id": 1,
          "order_id": 1,
          "product_id": 1,
          "quantity": 2,
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
              }
            ]
          },
          "excluded_ingredients": [
            {
              "id": 1,
              "name": "Lechuga"
            }
          ]
        }
      ]
    }
  ]
}
```

**Detalles incluidos en cada pedido:**
- **id**: Identificador único del pedido
- **status**: Estado actual (`pending`, `preparing`, `completed`)
- **payment_method**: Método de pago (`efectivo` o `transferencia`)
- **phone**: Teléfono del cliente (opcional, usado para sistema de recompensas)
- **customer_name**: Nombre del cliente
- **address**: Dirección de entrega (requerido si `delivery_type` es `domicilio`)
- **delivery_type**: Tipo de entrega (`domicilio` o `recoger`)
- **created_at**: Fecha y hora de creación (usado para ordenar)
- **updated_at**: Última actualización
- **started_at**: Fecha y hora cuando se comenzó a preparar (null si aún no se inició)
- **completed_at**: Fecha y hora cuando se completó (null si aún no se completó)
- **time_since_received**: Tiempo transcurrido desde que se recibió el pedido (en segundos)
- **time_since_received_formatted**: Tiempo formateado (ej: "5:30" = 5 minutos 30 segundos)
- **preparation_time**: Tiempo que duró en preparación (en segundos, null si no se inició)
- **preparation_time_formatted**: Tiempo de preparación formateado
- **total_time**: Tiempo total desde recepción hasta completado o actual (en segundos)
- **total_time_formatted**: Tiempo total formateado
- **items**: Array con todos los items del pedido
  - **product**: Información del producto (nombre, tipo, precio)
    - **default_ingredients**: Ingredientes por defecto del producto
  - **quantity**: Cantidad del producto
  - **excluded_ingredients**: Array con los ingredientes que NO se quieren (excluidos)

## Crear Pedido

### Crear un nuevo pedido

**Endpoint:** `POST /api/orders`

**Body de la solicitud:**
```json
{
  "payment_method": "efectivo",
  "phone": "+1234567890",
  "customer_name": "Juan Pérez",
  "address": "Calle Principal 123, Ciudad",
  "delivery_type": "domicilio",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "excluded_ingredients": [1]
    }
  ]
}
```

**Parámetros:**
- `payment_method` (opcional): Método de pago (`efectivo` o `transferencia`)
- `phone` (opcional): Teléfono del cliente (usado para sistema de recompensas)
- `customer_name` (opcional): Nombre del cliente
- `address` (opcional): Dirección de entrega. **Requerido si `delivery_type` es `domicilio`**
- `delivery_type` (opcional): Tipo de entrega (`domicilio` o `recoger`). Default: `recoger`
- `items` (requerido): Array de items del pedido
  - `product_id` (requerido): ID del producto
  - `quantity` (requerido): Cantidad del producto
  - `excluded_ingredients` (opcional): Array de IDs de ingredientes que NO se quieren

**Ejemplo de solicitud - Envío a domicilio:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "efectivo",
    "phone": "+1234567890",
    "customer_name": "Juan Pérez",
    "address": "Calle Principal 123, Ciudad",
    "delivery_type": "domicilio",
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "excluded_ingredients": [1]
      }
    ]
  }'
```

**Ejemplo de solicitud - Recoger en tienda:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "transferencia",
    "phone": "+1234567890",
    "customer_name": "María García",
    "delivery_type": "recoger",
    "items": [
      {
        "product_id": 1,
        "quantity": 1
      }
    ]
  }'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Pedido creado exitosamente",
  "order": {
    "id": 1,
    "status": "pending",
    "payment_method": "efectivo",
    "phone": "+1234567890",
    "customer_name": "Juan Pérez",
    "address": "Calle Principal 123, Ciudad",
    "delivery_type": "domicilio",
    "discount": 10,
    "discount_message": "¡Felicidades! Tienes un 10% de descuento por tus pedidos completados.",
    ...
  }
}
```

## Cambiar Estado del Pedido

### Actualizar estado de un pedido

**Endpoint:** `PATCH /api/orders/{id}/status`

**Descripción:** Permite cambiar el estado de un pedido. Los estados disponibles son:
- `pending`: Pedido pendiente (recién creado)
- `preparing`: Pedido en proceso de preparación
- `completed`: Pedido completado

**Parámetros:**
- `id` (en la URL): ID del pedido a actualizar

**Body de la solicitud:**
```json
{
  "status": "preparing"
}
```

**Ejemplo de solicitud:**
```bash
curl -X PATCH http://localhost:8000/api/orders/1/status \
  -H "Content-Type: application/json" \
  -d '{"status": "preparing"}'
```

**Ejemplo de respuesta:**
```json
{
  "message": "Estado del pedido actualizado",
  "order": {
    "id": 1,
    "status": "completed",
    "payment_method": "efectivo",
    "phone": "+1234567890",
    "customer_name": "Juan Pérez",
    "address": "Calle Principal 123, Ciudad",
    "delivery_type": "domicilio",
    "completed_orders_count": 6,
    "reward_earned": {
      "discount_percentage": 10,
      "order_count": 6,
      "message": "¡Felicidades! Has completado 6 pedidos. Tu próximo pedido tendrá un 10% de descuento."
    },
    ...
  }
}
```

**Nota sobre tiempos:**
- Cuando cambias el estado a `preparing`, se registra automáticamente `started_at` con la hora actual
- Cuando cambias el estado a `completed`, se registra automáticamente `completed_at` con la hora actual
- Los tiempos se calculan automáticamente y se muestran tanto en segundos como en formato legible (minutos:segundos)

## Obtener Siguiente Pedido Pendiente

### Obtener el siguiente pedido pendiente

**Endpoint:** `GET /api/orders/next`

**Descripción:** Retorna el pedido más antiguo con status "pending".

**Ejemplo de solicitud:**
```bash
curl -X GET http://localhost:8000/api/orders/next
```

**Ejemplo de respuesta:**
```json
{
  "order": {
    "id": 1,
    "status": "pending",
    "customer_name": "Juan Pérez",
    "address": "Calle Principal 123, Ciudad",
    "delivery_type": "domicilio",
    ...
  }
}
```

Si no hay pedidos pendientes:
```json
{
  "message": "No hay pedidos pendientes"
}
```

## Flujo de Trabajo Recomendado

1. **Obtener todos los pedidos** con `GET /api/orders` para ver la lista completa ordenada del más antiguo al más reciente.

2. **Identificar el siguiente pedido pendiente** (status: `pending`) de la lista.

3. **Cambiar el estado a "preparing"** cuando comiences a preparar el pedido:
   ```bash
   PATCH /api/orders/{id}/status
   Body: {"status": "preparing"}
   ```
   - Esto registra automáticamente `started_at` y comienza a contar el tiempo de preparación

4. **Cambiar el estado a "completed"** cuando termines de preparar el pedido:
   ```bash
   PATCH /api/orders/{id}/status
   Body: {"status": "completed"}
   ```
   - Esto registra automáticamente `completed_at` y calcula el tiempo total
   - Si el cliente tiene teléfono y alcanzó una recompensa, se aplica automáticamente

## Estados del Pedido

| Estado | Descripción |
|--------|-------------|
| `pending` | Pedido recién creado, esperando ser procesado |
| `preparing` | Pedido en proceso de preparación |
| `completed` | Pedido completado y listo |

## Tipos de Entrega

| Tipo | Descripción |
|------|-------------|
| `domicilio` | Envío a domicilio (requiere dirección) |
| `recoger` | Cliente recoge en tienda (no requiere dirección) |

## Ingredientes: Sistema de Exclusión

**Cómo funciona:**
1. Cada producto tiene ingredientes por defecto definidos en la base de datos
2. Al crear un pedido, solo se especifican los ingredientes que **NO se quieren** (excluidos)
3. El producto se prepara con todos sus ingredientes por defecto **excepto** los excluidos

**Ejemplo:**
- Producto "Hamburguesa Especial" tiene por defecto: Lechuga, Tomate, Cebolla, Queso
- Cliente pide: "Especial sin lechuga"
- Se envía: `excluded_ingredients: [1]` (donde 1 es el ID de Lechuga)
- Resultado: Hamburguesa con Tomate, Cebolla y Queso (sin Lechuga)

## Información de Tiempos

La API calcula automáticamente los siguientes tiempos:

- **time_since_received**: Tiempo transcurrido desde que se recibió el pedido (desde `created_at`)
- **preparation_time**: Tiempo que duró en preparación (desde `started_at` hasta `completed_at` o ahora)
- **total_time**: Tiempo total desde recepción hasta completado o actual (desde `created_at` hasta `completed_at` o ahora)

Los tiempos se muestran en dos formatos:
- En segundos (para cálculos): `time_since_received`, `preparation_time`, `total_time`
- Formateado (para visualización): `time_since_received_formatted`, `preparation_time_formatted`, `total_time_formatted`

Formato de tiempo: `MM:SS` (minutos:segundos) o `HH:MM:SS` (horas:minutos:segundos) si es mayor a 1 hora.

## Sistema de Recompensas

El sistema de recompensas está integrado con los pedidos. Para más información sobre cómo configurar recompensas, consulta [README_REWARDS.md](./README_REWARDS.md).

**Resumen:**
- Los clientes pueden obtener descuentos basados en el número de pedidos completados
- El teléfono se usa para rastrear los pedidos de cada cliente
- Al completar un pedido, se verifica automáticamente si el cliente alcanzó alguna recompensa
- El contador se reinicia automáticamente al alcanzar la última recompensa

## Notas

- Los pedidos se ordenan por `created_at` de forma ascendente (más antiguo primero).
- Cada pedido incluye todos sus items con productos, ingredientes por defecto e ingredientes excluidos.
- El estado se valida antes de actualizar (solo acepta: `pending`, `preparing`, `completed`).
- El método de pago es opcional al crear un pedido (`efectivo` o `transferencia`).
- El teléfono es opcional pero necesario para participar en el sistema de recompensas.
- Si `delivery_type` es `domicilio`, se recomienda incluir `address` y `customer_name`.
- Los tiempos se actualizan automáticamente cuando cambias el estado del pedido.
- `started_at` se registra automáticamente cuando cambias a `preparing`.
- `completed_at` se registra automáticamente cuando cambias a `completed`.
- Los ingredientes excluidos se guardan en la relación `ingredient_order_item` (representan exclusiones).
- Los ingredientes por defecto se definen en la tabla `product_ingredient`.



