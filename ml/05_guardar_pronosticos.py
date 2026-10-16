"""Guarda el pronóstico en la tabla `pronosticos` de MySQL.

Se ejecuta después de 04_pronostico.py. Reemplaza el contenido de la tabla
para que la app siempre muestre el último pronóstico calculado.
"""

import pandas as pd
from sqlalchemy import text

from conexion import DIR_DATOS, motor

INSERT = """
    INSERT INTO pronosticos
        (serie, anio, mes, valor, limite_inferior, limite_superior, created_at, updated_at)
    VALUES
        (:serie, :anio, :mes, :valor, :limite_inferior, :limite_superior, NOW(), NOW())
"""


def main():
    df = pd.read_csv(DIR_DATOS / "pronosticos.csv")
    with motor().begin() as con:
        con.execute(text("DELETE FROM pronosticos"))
        con.execute(text(INSERT), df.to_dict("records"))
    print(f"{len(df)} pronósticos guardados en la tabla `pronosticos`")


if __name__ == "__main__":
    main()
