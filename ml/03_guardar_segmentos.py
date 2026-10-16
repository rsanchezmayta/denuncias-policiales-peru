"""Guarda los segmentos calculados en la tabla `segmentos` de MySQL.

Se ejecuta después de 02_clustering.py. Reemplaza el contenido de la tabla
(son solo ~196 filas), de modo que la app siempre muestra el último resultado.
"""

import pandas as pd
from sqlalchemy import text

from conexion import DIR_DATOS, motor

INSERT = """
    INSERT INTO segmentos
        (provincia_id, cluster, total, crecimiento, modalidad_dominante,
         pct_nacional, created_at, updated_at)
    VALUES
        (:provincia_id, :cluster, :total, :crecimiento, :modalidad_dominante,
         :pct_nacional, NOW(), NOW())
"""


def main():
    df = pd.read_csv(DIR_DATOS / "segmentos.csv")
    with motor().begin() as con:
        con.execute(text("DELETE FROM segmentos"))
        con.execute(text(INSERT), df.to_dict("records"))
    print(f"{len(df)} segmentos guardados en la tabla `segmentos`")


if __name__ == "__main__":
    main()
