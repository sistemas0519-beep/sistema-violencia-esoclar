# Especificación del Dashboard — Personal de Apoyo

## Resumen General

Panel Filament dedicado para **Psicólogos** y **Asistentes** de la institución, accesible en `/apoyo`. Gestiona casos sensibles, asesorías, sesiones, intervenciones, comunicación interna y métricas de desempeño.

---

## 1. Gestión de Casos Sensibles

### Descripción
Vista de casos marcados como sensibles con restricción por nivel de confidencialidad.

### Funcionalidades
- Listado filtrable por estado, nivel de sensibilidad, área temática y nivel de urgencia.
- **Asistentes** no pueden ver casos con nivel `altamente_confidencial`.
- Cada visualización y edición queda registrada en `acceso_casos`.
- Notas confidenciales con 3 niveles de visibilidad: `solo_autor`, `psicologos`, `equipo_apoyo`.
- Seguimientos e intervenciones asociados al caso.

### Campos del caso (extensiones)
| Campo | Tipo | Valores |
|---|---|---|
| `es_sensible` | boolean | — |
| `nivel_sensibilidad` | enum | normal, confidencial, altamente_confidencial |
| `area_tematica` | enum | acoso, violencia_fisica, violencia_psicologica, ciberacoso, violencia_sexual, discriminacion, autolesion, otro |
| `nivel_urgencia` | enum | bajo, medio, alto, critico |
| `motivo_sensibilidad` | text | — |

---

## 2. Módulo de Asesoría y Apoyo

### 2.1 Solicitudes de Asesoría
- Código auto-generado `SOL-YYYYMMDD-XXXX`.
- Estados: `pendiente` → `asignada` → `en_proceso` → `completada` / `cancelada`.
- Acción rápida "Tomar solicitud" que asigna al usuario actual.
- Cálculo automático de tiempo de respuesta y resolución.

### 2.2 Sesiones
- Programación de sesiones con fecha, hora inicio/fin, tipo (individual, grupal, familiar, seguimiento).
- Filtrado automático por profesional autenticado.
- Acciones: completar (requiere notas/observaciones), cancelar (requiere motivo), marcar no asistió.
- **Calendario visual** con vista mensual código de colores por estado.

### 2.3 Intervenciones
- Código auto-generado `INT-YYYYMMDD-XXXX`.
- Tipos: individual, grupal, familiar, crisis, preventiva, derivacion.
- Seguimiento de efectividad (escala 1-10) y seguimiento requerido.
- Acciones: completar con resultados y recomendaciones.

---

## 3. Comunicación Interna

### Mensajes
- Sistema de mensajería asíncrona entre personal de apoyo.
- Hilos de conversación (`hilo_id`).
- Seguimiento de lectura (`leido_en`).
- Prioridad: normal, urgente, informativo.
- Archivado por remitente/destinatario independiente.
- Polling cada 15 segundos.

### Recursos de Apoyo (Base de Conocimientos)
- Documentos categorizados: guía, protocolo, formato, recurso_didactico, normativa.
- Búsqueda por título, contenido y etiquetas.
- Contador de visitas.
- Área temática alineada con tipos de caso.

---

## 4. Seguridad y Privacidad

### Políticas de acceso
| Entidad | Psicólogo | Asistente |
|---|---|---|
| Casos altamente confidenciales | ✅ Ver/Editar | ❌ |
| Casos confidenciales | ✅ Ver/Editar | ✅ Solo ver |
| Notas `solo_autor` | Solo autor | Solo autor |
| Notas `psicologos` | ✅ | ❌ |
| Notas `equipo_apoyo` | ✅ | ✅ |
| Sesiones propias | CRUD | CRUD |
| Intervenciones | CRUD | CRUD (no eliminar) |
| Mensajes | Solo participantes | Solo participantes |

### Auditoría
- Todo acceso a caso sensible registrado en `acceso_casos` (usuario, IP, acción, timestamp).
- AuditLog personal visible en "Registro de Auditoría".
- Logs inmutables (sin update/delete).

---

## 5. Métricas y Reportes

### KPIs del Dashboard
- **Casos activos** asignados al profesional.
- **Sesiones del día**.
- **Solicitudes pendientes**.
- **Intervenciones activas**.
- **Mensajes sin leer**.

### Página de Métricas
- Distribución de casos por estado, tipo y área temática.
- Estadísticas de sesiones (completadas vs canceladas, tasa de asistencia, horas dedicadas).
- Efectividad de intervenciones (promedio, por tipo, tasa de completitud).
- Tiempos promedio de respuesta y resolución.
- Tendencia semanal de 8 semanas.
- Filtro por período: última semana, último mes, último año.

### Widgets del Dashboard
1. **Stats Overview**: 5 tarjetas KPI con tendencias.
2. **Sesiones Hoy**: Tabla con sesiones del día.
3. **Casos Urgentes**: Casos creados hace >72hrs sin resolver, sensibles o urgentes.
4. **Carga de Trabajo**: Distribución del equipo (asignaciones activas, sesiones de la semana).

---

## 6. Tablas de Base de Datos

### Nuevas tablas
| Tabla | Descripción |
|---|---|
| `acceso_casos` | Log de accesos a casos sensibles |
| `notas_confidenciales` | Notas con niveles de visibilidad (soft delete) |
| `solicitudes_asesoria` | Solicitudes de asesoría con tracking de tiempos |
| `sesiones` | Sesiones programadas con paciente/profesional |
| `intervenciones` | Intervenciones con efectividad (soft delete) |
| `mensajes` | Mensajería interna con hilos (soft delete) |
| `recursos_apoyo` | Base de conocimientos (soft delete) |

### Campos agregados a `casos`
`es_sensible`, `nivel_sensibilidad`, `area_tematica`, `nivel_urgencia`, `motivo_sensibilidad`

### Campo agregado a `users.rol`
Nuevo valor: `asistente`

---

## 7. Estructura de Archivos

```
app/Filament/Apoyo/
├── Pages/
│   ├── CalendarioSesiones.php
│   ├── MetricasReportes.php
│   └── RegistroAuditoria.php
├── Resources/
│   ├── CasoSensibleResource/
│   ├── IntervencionResource/
│   ├── MensajeResource/
│   ├── RecursoApoyoResource/
│   ├── SesionResource/
│   └── SolicitudAsesoriaResource/
└── Widgets/
    ├── ApoyoStatsOverview.php
    ├── CargaTrabajoWidget.php
    ├── CasosUrgentesWidget.php
    └── SesionesHoyWidget.php

app/Models/
├── AccesoCaso.php
├── Intervencion.php
├── Mensaje.php
├── NotaConfidencial.php
├── RecursoApoyo.php
├── Sesion.php
└── SolicitudAsesoria.php

app/Policies/
├── CasoSensiblePolicy.php
├── IntervencionPolicy.php
├── MensajePolicy.php
├── NotaConfidencialPolicy.php
└── SesionPolicy.php
```
