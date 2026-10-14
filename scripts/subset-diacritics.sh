#!/usr/bin/env bash
# Pravi resources/fonts/*-sr-wght.woff2 — samo slova č ć š ž đ (mala i velika)
# iz latin-ext skupova paketa @fontsource-variable. Ceo latin-ext Unbounded
# fonta ima 118 KB, a nama treba samo deset glifova.
# Potreban je fonttools: pip install fonttools brotli
set -euo pipefail
cd "$(dirname "$0")/.."
for f in unbounded manrope; do
    pyftsubset "node_modules/@fontsource-variable/$f/files/$f-latin-ext-wght-normal.woff2" \
        --unicodes=U+0106-0107,U+010C-010D,U+0110-0111,U+0160-0161,U+017D-017E \
        --flavor=woff2 --layout-features='*' \
        --output-file="resources/fonts/$f-sr-wght.woff2"
done
echo "resources/fonts je ažuriran"
