#!/usr/bin/env bash
#
# Syntax-check every PHP file in the theme.
#
# A WordPress theme has no build step, so a PHP parse error is not caught by
# anything before it reaches the server -- where it shows up as a white screen
# on the live site. This is the cheapest possible guard against that.
#
# Usage: bin/lint-php.sh
# Exits non-zero if any file fails to parse.

set -uo pipefail

cd "$(dirname "$0")/.." || exit 1

if ! command -v php >/dev/null 2>&1; then
  echo "error: php not found on PATH."
  echo
  echo "  Windows : winget install PHP.PHP"
  echo "  macOS   : brew install php"
  echo "  Linux   : sudo apt install php-cli"
  exit 127
fi

echo "Linting with $(php -r 'echo PHP_VERSION;')"
echo

failed=0
checked=0

while IFS= read -r file; do
  checked=$((checked + 1))
  if ! output=$(php -l "$file" 2>&1); then
    echo "FAIL  $file"
    echo "$output" | sed 's/^/      /'
    failed=$((failed + 1))
  fi
done < <(find . -name '*.php' -not -path './.git/*' -not -path './vendor/*' | sort)

echo
if [ "$failed" -gt 0 ]; then
  echo "$failed of $checked file(s) failed to parse."
  exit 1
fi

echo "All $checked PHP file(s) parsed cleanly."
