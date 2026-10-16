# Denuncias Policiales Perú

Panel de analítica de denuncias policiales del Perú (fuente: **SIDPOL – MININTER**, datos abiertos), con dashboard interactivo, segmentación de provincias (K-means), pronóstico de series de tiempo (SARIMA) y una API JSON.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.14-3776AB?logo=python&logoColor=white)
![scikit-learn](https://img.shields.io/badge/scikit--learn-1.9-F7931E?logo=scikitlearn&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

## Descripción

A partir del dataset público de denuncias policiales (Ene 2018 – Jul 2026 · ~369 mil registros · 7.3 millones de denuncias), este proyecto:

- **Ingesta** el CSV a MySQL mediante un seeder en dos pasadas.
- **Visualiza** con un dashboard filtrable (KPIs + gráficos Chart.js con drill-down).
- **Segmenta** las 196 provincias en 4 perfiles de incidencia con K-means.
- **Pronostica** los próximos 12 meses con SARIMA (MAPE ≈ 3.5 %).
- **Expone** una API JSON de solo lectura.
- **Mapea** los departamentos en un mapa coroplético (Leaflet).

## Funcionalidades

- Autenticación propia (login/logout) sin starter kit.
- Dashboard con filtros por año, departamento y modalidad; gráficos interactivos (clic para filtrar).
- Explorador de denuncias con búsqueda, orden y paginación.
- Exportación a CSV de los resultados filtrados.
- CRUD del catálogo de modalidades (con protección para no borrar datos en cascada).
- Modo claro / oscuro.
- Mapa coroplético por departamento.
- API JSON.

## Stack

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 13, PHP 8.3+ |
| Base de datos | MySQL / MariaDB |
| Frontend | Blade + Tailwind CSS (CDN) + Chart.js + Leaflet |
| Análisis de datos | Python 3.14, pandas, scikit-learn, statsmodels |

## Modelo de datos

Esquema en estrella:

```
departamentos ─< provincias ─< distritos ─< denuncias >─ modalidades
                                              (anio, mes, cantidad)

segmentos     (resultado del clustering)  → provincia_id, cluster, total, ...
pronosticos   (resultado del pronóstico)  → serie, anio, mes, valor, límites
```

## Requisitos

- PHP 8.3+ y Composer
- MySQL o MariaDB (XAMPP sirve)
- Python 3.10+ (para el pipeline de ML)
- Git

## Instalación

```bash
# 1. Clonar e instalar dependencias
git clone https://github.com/rsanchezmayta/denuncias-policiales-peru.git
cd denuncias-policiales-peru
composer install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Crear la base de datos (MySQL) y ajustar credenciales en .env
#    CREATE DATABASE denuncias_policiales_peru CHARACTER SET utf8mb4;

# 4. Migrar e importar datos
php artisan migrate
php artisan db:seed        # importa el CSV (usa la muestra si no está el completo)
```

### Dataset

El CSV completo **no se incluye** en el repositorio (pesa ~27 MB). Descárgalo desde el
Portal de Datos Abiertos del Perú (buscando *"Denuncias Policiales"* — SIDPOL) y colócalo en:

```
database/data/denuncias_policiales.csv
```

> Nota: el portal `datosabiertos.gob.pe` bloquea descargas automáticas; la descarga suele hacerse
> manualmente desde el navegador. Si no colocas el archivo, el seeder usará automáticamente la
> muestra `database/data/ejemplo_denuncias.csv`.

## Pipeline de Machine Learning

Los scripts viven en `ml/` y escriben sus resultados en tablas de MySQL que Laravel grafica.

```bash
cd ml
python3 -m venv .venv
source .venv/bin/activate
pip install pandas scikit-learn sqlalchemy pymysql statsmodels

python 01_extraer.py             # extrae agregados a ml/data/*.csv
python 02_clustering.py          # K-means de provincias
python 03_guardar_segmentos.py   # guarda en la tabla `segmentos`
python 04_pronostico.py          # SARIMA → próximos 12 meses
python 05_guardar_pronosticos.py # guarda en la tabla `pronosticos`
```

| Modelo | Técnica | Resultado |
|--------|---------|-----------|
| Segmentación de provincias | K-means (intensidad de denuncias) | 4 segmentos |
| Pronóstico mensual nacional | SARIMA(1,1,1)(1,1,1,12) | MAPE ≈ 3.5 % |

## Uso

```bash
php artisan serve
```

Abre http://127.0.0.1:8000 e inicia sesión con:

| Correo | Contraseña |
|--------|------------|
| `admin@denuncias.test` | `admin12345` |

## API JSON

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/denuncias` | Listado paginado (`per_page`, filtros) |
| GET | `/api/denuncias/resumen` | Totales por año y modalidad |
| GET | `/api/segmentos` | Segmentos por provincia |

Filtros soportados: `anio`, `mes`, `departamento_id`, `modalidad_id`, `distrito`.

## Estructura del proyecto

```
app/
  Http/Controllers/        Dashboard, Denuncias, Segmentos, Pronóstico, Mapa, Modalidades, Api
  Http/Requests/           Validaciones
  Http/Resources/          Recursos de la API
  Models/                  Eloquent
  Support/Filtros.php      Lógica de filtros reutilizable
database/
  data/                    CSV + muestra
  migrations/  seeders/
ml/                        Pipeline de análisis (Python)
resources/views/           Blade (layout, dashboard, denuncias, mapa, ...)
routes/  tests/
```

## Licencia

[MIT](LICENSE) © 2026 Robinson Josué Sánchez Mayta
