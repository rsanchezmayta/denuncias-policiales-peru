"""Conexión a la base de datos de Laravel leyendo las credenciales del archivo .env."""

from pathlib import Path

from sqlalchemy import create_engine, text

RAIZ = Path(__file__).resolve().parent.parent
DIR_DATOS = Path(__file__).resolve().parent / "data"


def _leer_env() -> dict:
    env = {}
    for linea in (RAIZ / ".env").read_text().splitlines():
        linea = linea.strip()
        if not linea or linea.startswith("#") or "=" not in linea:
            continue
        clave, valor = linea.split("=", 1)
        env[clave.strip()] = valor.strip().strip('"')
    return env


def motor():
    env = _leer_env()
    url = (
        f"mysql+pymysql://{env.get('DB_USERNAME', 'root')}:{env.get('DB_PASSWORD', '')}"
        f"@{env.get('DB_HOST', '127.0.0.1')}:{env.get('DB_PORT', '3306')}"
        f"/{env['DB_DATABASE']}?charset=utf8mb4"
    )
    return create_engine(url, future=True)


def leer(sql: str):
    import pandas as pd

    with motor().connect() as con:
        return pd.read_sql(text(sql), con)


def ejecutar(sql: str, parametros: dict | None = None):
    with motor().begin() as con:
        con.execute(text(sql), parametros or {})


if __name__ == "__main__":
    DIR_DATOS.mkdir(exist_ok=True)
    prueba = leer("SELECT COUNT(*) AS filas, SUM(cantidad) AS total FROM denuncias")
    print(prueba.to_string(index=False))
