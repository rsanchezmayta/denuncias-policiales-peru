"""Clustering de provincias con K-means (Fase 2a).

Segmenta las provincias según la intensidad de denuncias (log del total),
que es la característica que mejor estructura los datos (silueta ~0.53).

El crecimiento y la modalidad dominante NO se usan para agrupar, pero sí
se guardan como descriptores de cada segmento.

Resultado -> ml/data/segmentos.csv
"""

import sys

import numpy as np
import pandas as pd
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from sklearn.preprocessing import StandardScaler

from conexion import DIR_DATOS

FEATURES = ["log_total"]
MIN_TAMANO = 5
ANIOS_BASE = [2018, 2019, 2020]
ANIOS_RECIENTES = [2023, 2024, 2025]


def cargar() -> pd.DataFrame:
    mod = pd.read_csv(DIR_DATOS / "provincias_modalidad.csv")
    anio = pd.read_csv(DIR_DATOS / "provincias_anio.csv")

    total = mod.groupby(["provincia_id", "provincia", "departamento"], as_index=False)["total"].sum()

    comp = mod.pivot_table(
        index="provincia_id", columns="modalidad", values="total", aggfunc="sum", fill_value=0
    )
    comp_pct = comp.div(comp.sum(axis=1), axis=0)

    base = anio[anio["anio"].isin(ANIOS_BASE)].groupby("provincia_id")["total"].mean()
    reciente = anio[anio["anio"].isin(ANIOS_RECIENTES)].groupby("provincia_id")["total"].mean()
    crec = (reciente / base.replace(0, np.nan)).rename("crecimiento")

    df = total.set_index("provincia_id").join(crec)
    df["crecimiento"] = df["crecimiento"].fillna(1.0)
    df["log_total"] = np.log1p(df["total"])
    df["modalidad_dominante"] = comp_pct.idxmax(axis=1)
    df["pct_nacional"] = df["total"] / df["total"].sum()

    return df.reset_index(), list(comp.columns)


def fusionar_pequenos(Xs, labels, min_tamano):
    labels = np.array(labels, dtype=int)
    while True:
        tam = pd.Series(labels).value_counts()
        pequenos = tam[tam < min_tamano].index.tolist()
        if not pequenos:
            return labels
        grandes = tam[tam >= min_tamano].index.tolist()
        centroides = {c: Xs[labels == c].mean(axis=0) for c in grandes}
        for chico in pequenos:
            for i in np.where(labels == chico)[0]:
                labels[i] = min(grandes, key=lambda c: np.linalg.norm(Xs[i] - centroides[c]))


def evaluar_k(Xs, rango=range(2, 9)):
    print("  k   inercia      silueta   tamaños")
    for k in rango:
        km = KMeans(n_clusters=k, n_init=10, random_state=42).fit(Xs)
        lab = fusionar_pequenos(Xs, km.labels_, MIN_TAMANO)
        sil = silhouette_score(Xs, lab)
        tam = sorted(pd.Series(lab).value_counts().tolist(), reverse=True)
        print(f"  {k}   {km.inertia_:10.1f}   {sil:.4f}   {tam}")


def main():
    k_arg = int(sys.argv[1]) if len(sys.argv) > 1 else None
    df, modalidades = cargar()
    print(f"Provincias: {len(df)}   Features: {FEATURES}\n")

    Xs = StandardScaler().fit_transform(df[FEATURES])
    evaluar_k(Xs)
    k = k_arg or 4

    km = KMeans(n_clusters=k, n_init=10, random_state=42).fit(Xs)
    df["cluster"] = fusionar_pequenos(Xs, km.labels_, MIN_TAMANO)

    orden = df.groupby("cluster")["total"].mean().sort_values(ascending=False).index
    df["cluster"] = df["cluster"].map({v: n for n, v in enumerate(orden)})

    print(f"\n=== Perfil de los {df['cluster'].nunique()} segmentos (k={k}) ===")
    for c, g in df.groupby("cluster"):
        print(
            f"\nSegmento {c}: {len(g)} provincias"
            f"\n  total medio ........ {g['total'].mean():,.0f}"
            f"\n  % del total país ... {g['pct_nacional'].sum():.1%}"
            f"\n  crecimiento medio .. {g['crecimiento'].mean():.2f}"
            f"\n  modalidad dominante  {g['modalidad_dominante'].mode().iat[0]}"
        )
        print("   top: " + ", ".join(g.nlargest(3, "total")["provincia"]))

    salida = DIR_DATOS / "segmentos.csv"
    df[
        ["provincia_id", "provincia", "departamento", "cluster", "total",
         "crecimiento", "modalidad_dominante", "pct_nacional"]
    ].to_csv(salida, index=False)
    print(f"\nGuardado -> {salida}")


if __name__ == "__main__":
    main()
