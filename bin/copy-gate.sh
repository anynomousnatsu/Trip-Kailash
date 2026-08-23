#!/usr/bin/env bash
# The copy gate.
#
# Every viewer-facing line on this site is written deliberately in
# docs/design-package.md and ships verbatim. This checks that nothing drifted
# in on the way to the templates.
#
# Long generation slides toward corporate stock language even when told not to,
# and it lands in the lower sections rather than the hero, which is why this
# runs over everything rather than spot-checks.
#
# Run before showing anyone a build.
set -uo pipefail
cd "$(dirname "$0")/.."

TARGETS=$(find . -name '*.php' -not -path './.git/*' -not -path './docs/*')
FAIL=0

report () {
  if [ -n "$2" ]; then
    echo "FAIL  $1"
    echo "$2" | sed 's/^/      /'
    FAIL=1
  else
    echo "ok    $1"
  fi
}

# Em dashes. Commas and periods instead, everywhere.
report "no em dashes" "$(grep -n '—' $TARGETS 2>/dev/null)"

# The stock words. Each one is a tell that the sentence wrote itself.
report "no stock words" "$(grep -rniE 'leverage|seamless|empower|unlock|robust|actionable|data-driven|solutions' $TARGETS 2>/dev/null)"

# The quieter tells.
report "no AI tells" "$(grep -rniE 'testament|delve|elevate|landscape of|not just [a-z]+, it' $TARGETS 2>/dev/null)"

# Placeholders must never reach a page. A site whose central argument is
# "check our numbers" cannot print a bracket where a number belongs.
report "no placeholders" "$(grep -rnE '\[NUMBER\]|\[ADDRESS\]|\[X\]|lorem ipsum' $TARGETS 2>/dev/null)"

echo
if [ "$FAIL" -eq 0 ]; then
  echo "Copy gate passed."
else
  echo "Copy gate FAILED. Rewrite each hit in plain language and re-run."
fi
exit $FAIL
