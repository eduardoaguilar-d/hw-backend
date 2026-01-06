# API de Gestión de Pedidos - Hamburguesas y Hotdogs

API backend para gestionar pedidos de hamburguesas y hotdogs con ingredientes personalizados, información del cliente, método de pago, seguimiento de tiempos y sistema de recompensas.

## Documentación

Esta API está dividida en dos módulos principales:

### 📦 [Gestión de Pedidos](./README_ORDERS.md)
Documentación completa sobre cómo crear, visualizar y gestionar pedidos:
- Crear pedidos con información del cliente
- Visualizar pedidos ordenados
- Cambiar estados de pedidos
- Sistema de ingredientes excluidos
- Seguimiento de tiempos
- Tipos de entrega (domicilio/recoger)

### 🎁 [Gestión de Recompensas](./README_REWARDS.md)
Documentación completa sobre el sistema dinámico de recompensas:
- Configurar recompensas
- Crear, editar y eliminar recompensas
- Sistema de reinicio automático
- Descuentos personalizables

### 🍔 [Gestión del Menú](./README_MENU.md)
Documentación completa sobre productos e ingredientes:
- Gestionar productos (hamburguesas y hotdogs)
- Gestionar ingredientes
- Configurar ingredientes por defecto de productos
- Consultar el menú completo

## Características Principales

- ✅ **Pedidos completos**: Nombre, dirección, teléfono, tipo de entrega
- ✅ **Ingredientes personalizados**: Sistema de exclusión de ingredientes
- ✅ **Seguimiento de tiempos**: Tiempo de recepción, preparación y total
- ✅ **Recompensas dinámicas**: Sistema configurable de descuentos
- ✅ **Métodos de pago**: Efectivo y transferencia
- ✅ **Tipos de entrega**: Domicilio y recoger en tienda

## Inicio Rápido

### Ver todos los pedidos
```bash
GET /api/orders
```

### Crear un pedido
```bash
POST /api/orders
Body: {
  "customer_name": "Juan Pérez",
  "phone": "+1234567890",
  "address": "Calle Principal 123",
  "delivery_type": "domicilio",
  "payment_method": "efectivo",
  "items": [...]
}
```

### Gestionar recompensas
```bash
GET /api/rewards
POST /api/rewards
PUT /api/rewards/{id}
DELETE /api/rewards/{id}
```

### Consultar el menú
```bash
GET /api/products
GET /api/ingredients
```

### Gestionar productos e ingredientes
```bash
POST /api/products
PUT /api/products/{id}
PUT /api/products/{id}/ingredients
POST /api/ingredients
PUT /api/ingredients/{id}
```

## Estructura de la Base de Datos

- **orders**: Pedidos con información del cliente y estado
- **products**: Productos (hamburguesas/hotdogs) con precio
- **ingredients**: Ingredientes disponibles
- **product_ingredient**: Ingredientes por defecto de cada producto
- **order_items**: Items de cada pedido
- **ingredient_order_item**: Ingredientes excluidos de cada item
- **reward_configs**: Configuración de recompensas

## Tecnologías

- Laravel 12
- SQLite (configurable para MySQL/PostgreSQL)
- API REST

## Licencia

MIT
