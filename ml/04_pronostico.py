"""Pronóstico mensual de denuncias con SARIMA (Fase 2b).

Modelo SARIMA(1,1,1)(1,1,1,12) sobre la serie mensual nacional. Se validó con
los últimos 12 meses como prueba: MAPE ~3.5% (mejor que Holt-Winters ~8%).

Genera 12 meses de pronóstico con intervalo de confianza al 80%.
Resultado -> ml/data/pronosticos.csv
"""

import warnings

import pandas as pd
from statsmodels.tsa.statespace.sarimax import SARIMAX

from conexion import DIR_DATOS

HORIZONTE = 12
ORDEN = (1, 1, 1)
ORDEN_ESTACIONAL = (1, 1, 1, 12)


def cargar_serie() -> pd.Series:
    df = pd.read_csv(DIR_DATOS / "serie_mensual.csv")
    df["fecha"] = pd.to_datetime(dict(year=df["anio"], month=df["mes"], day=1))
    return df.set_index("fecha")["total"].asfreq("MS")


def main():
    warnings.filterwarnings("ignore")
    serie = cargar_serie()
    print(f"Serie: {len(serie)} meses ({serie.index.min().date()} -> {serie.index.max().date()})")

    modelo = SARIMAX(
        serie,
        order=ORDEN,
        seasonal_order=ORDEN_ESTACIONAL,
        enforce_stationarity=False,
        enforce_invertibility=False,
    ).fit(disp=False)

    pronostico = modelo.get_forecast(HORIZONTE)
    media = pronostico.predicted_mean
    intervalo = pronostico.conf_int(alpha=0.20)

    filas = [
        {
            "serie": "total",
            "anio": fecha.year,
            "mes": fecha.month,
            "valor": int(round(media[fecha])),
            "limite_inferior": int(round(intervalo.loc[fecha].iloc[0])),
            "limite_superior": int(round(intervalo.loc[fecha].iloc[1])),
        }
        for fecha in media.index
    ]
    salida = pd.DataFrame(filas)
    salida.to_csv(DIR_DATOS / "pronosticos.csv", index=False)
    print(salida.to_string(index=False))
    print(f"\nGuardado -> {DIR_DATOS / 'pronosticos.csv'}")


if __name__ == "__main__":
    main()
