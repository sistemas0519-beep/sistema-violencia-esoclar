# Especificación Técnica: Panel de Asignación de Psicólogos

## 1. Arquitectura de Datos

### 1.1 Modelo Asignacion

```php
// Propiedades:
- id
- psicologo_id (FK -> users)
- paciente_id (FK -> users)
- caso_id (FK -> casos, nullable)
- notas (text, nullable)
- fecha_inicio (date)
- fecha_fin (date, nullable)
- frecuencia_atencion (enum: 'semanal', 'quincenal', 'mensual')
- dia_atencion (enum: 'lunes', 'martes', 'miercoles', 'jueves', 'viernes')
- hora_atencion (time)
- estado (enum: 'activa', 'finalizada', 'cancelada')
- motivo_fin (text, nullable)
- solicit_cambio (boolean) - paciente solicita cambio
- created_by (FK -> users)
- created_at
- updated_at
```

### 1.2扩展 User

- especialidad (varchar, nullable)
- disponibilidad (enum: 'disponible', 'ocupado', 'no_disponible')
- foto_perfil (varchar, nullable)

## 2. Características de Interfaz

### 2.1 Panel de Administración (AsignacionesPage)

- **Lista de Psicólogos**: Cards con foto, nombre, especialidad, disponibilidad
- **Lista de Pacientes Pendientes**: Casos sin asignar o usuarios que requieren psicólogo
- **Búsqueda y Filtros**: Por nombre, especialidad, disponibilidad
- **Dropdown de Selección**: Psicólogo y paciente
- **Formulario de Asignación**: Notas, fecha inicio, frecuencia, día, hora

### 2.2 Widget de Asignaciones Activas

- Tabla con columns: Paciente, Psicólogo, Fecha Inicio, Frecuencia, Estado, Acciones
- Acciones: Editar, Finalizar, Ver Historial

## 3. Lógica de Negocio

### 3.1 Validaciones

- Verificar disponibilidad del psicólogo antes de asignar
- Evitar asignaciones duplicadas activas para el mismo paciente
- Solo admin puede crear/editar asignaciones

### 3.2 Notificaciones

- Notification a psicólogo: Nueva asignación creada
- Notification a paciente: Psicólogo asignado

## 4. Auditoría

- Registro de: quién creó, fecha, cambios realizados
- Historial de asignaciones por paciente