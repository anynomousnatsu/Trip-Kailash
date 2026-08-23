#!/usr/bin/env bash
# Download the three self-hosted faces into assets/fonts/.
#
# The design package requires self-hosting: loading from fonts.googleapis.com
# adds a DNS lookup and a round trip that costs real seconds on the Indian and
# Nepali mobile connections most of this audience is on.
#
# All three are variable fonts, so one file covers every weight of that face.
# Three subsets per face: latin, latin-ext (which carries the IAST diacritics
# in terms like Dasa-Divasiya Siva-Parikrama), and devanagari for Noto.
#
# Re-run this only to update the faces. The files are committed to the repo.
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p assets/fonts
B=https://fonts.gstatic.com/s

get () { curl.exe -sSfL "$1" -o "assets/fonts/$2"; echo "  $2  $(wc -c < "assets/fonts/$2") bytes"; }

echo "Cinzel (display, 700 and 900):"
get "$B/cinzel/v26/8vIJ7ww63mVu7gt79mT7.woff2"      cinzel-latin.woff2
get "$B/cinzel/v26/8vIJ7ww63mVu7gt7-GT7LEc.woff2"   cinzel-latin-ext.woff2

echo "Karla (body, 400 and 500):"
get "$B/karla/v33/qkB9XvYC6trAT55ZBi1ueQVIjQTD-JrIH2G7nytkHRyQ8p4wUje6bg.woff2"     karla-latin.woff2
get "$B/karla/v33/qkB9XvYC6trAT55ZBi1ueQVIjQTD-JrIH2G7nytkHRyQ8p4wUjm6bnEr.woff2"   karla-latin-ext.woff2

echo "Noto Serif Devanagari (500 and 700):"
get "$B/notoserifdevanagari/v34/x3dNcl3IZKmUqiMk48ZHXJ5jwU-DZGRSaQ4Hh2dGyFzPLcQPVbnRH-xEMqTN.woff2"  noto-deva-devanagari.woff2
get "$B/notoserifdevanagari/v34/x3dNcl3IZKmUqiMk48ZHXJ5jwU-DZGRSaQ4Hh2dGyFzPLcQPVbnRH-1EMg.woff2"    noto-deva-latin.woff2
get "$B/notoserifdevanagari/v34/x3dNcl3IZKmUqiMk48ZHXJ5jwU-DZGRSaQ4Hh2dGyFzPLcQPVbnRH-NEMqTN.woff2"  noto-deva-latin-ext.woff2

echo
echo "Total: $(du -sh assets/fonts | cut -f1)"
