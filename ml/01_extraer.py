"""Extrae los agregados desde MySQL y los guarda como CSV en ml/data/.

Estos CSV son la entrada de los scripts de clustering y pronóstico.
"""

from conexion import DIR_DATOS, leer

CONSULTAS = {
    # Provincia x modalidad: para la composición del delito (clustering).
    "provincias_modalidad": """
        SELECT p.id AS provincia_id, p.nombre AS provincia, d.nombre AS departamento,
               m.nombre AS modalidad, SUM(de.cantidad) AS total
        FROM denuncias de
        JOIN distritos di ON de.distrito_id = di.id
        JOIN provincias p ON di.provincia_id = p.id
        JOIN departamentos d ON p.departamento_id = d.id
        JOIN modalidades m ON de.modalidad_id = m.id
        GROUP BY p.id, p.nombre, d.nombre, m.nombre
    """,
    # Provincia x año: para medir el crecimiento (clustering).
    "provincias_anio": """
        SELECT p.id AS provincia_id, de.anio AS anio, SUM(de.cantidad) AS total
        FROM denuncias de
        JOIN distritos di ON de.distrito_id = di.id
        JOIN provincias p ON di.provincia_id = p.id
        GROUP BY p.id, de.anio
    """,
    # Serie mensual total: para el pronóstico.
    "serie_mensual": """
        SELECT anio, mes, SUM(cantidad) AS total
        FROM denuncias
        GROUP BY anio, mes
        ORDER BY anio, mes
    """,
    # Serie mensual por modalidad: permite pronosticar por tipo de delito.
    "serie_modalidad_mensual": """
        SELECT de.anio AS anio, de.mes AS mes, m.nombre AS modalidad, SUM(de.cantidad) AS total
        FROM denuncias de
        JOIN modalidades m ON de.modalidad_id = m.id
        GROUP BY de.anio, de.mes, m.nombre
        ORDER BY de.anio, de.mes
    """,
}


def main():
    DIR_DATOS.mkdir(exist_ok=True)
    for nombre, sql in CONSULTAS.items():
        df = leer(sql)
        destino = DIR_DATOS / f"{nombre}.csv"
        df.to_csv(destino, index=False)
        print(f"{nombre:26s} -> {len(df):>5d} filas  ({destino.name})")


if __name__ == "__main__":
    main()
