#!/usr/bin/env python3
"""Guard the HTML cache window.

A long stale-while-revalidate on HTML is the single hardest deploy bug to
diagnose in this theme, because it fails in a way that looks like success.

stale-while-revalidate is honoured by browsers on navigation, not only by
CDNs. With a long window a returning visitor keeps being served the page
their browser already had, while every stylesheet and script on it updates
normally, because assets are fetched by URL and the version string changed.

The result is a site that renders the OLD template with the NEW design
tokens. It presents as "the colours and fonts changed and nothing else did",
and it survives every check a developer makes, because a hard refresh, curl,
and any fresh URL all return the correct page.

This cost a full debugging session once. It should not cost a second one.
"""
import re
import sys
from pathlib import Path

MAX_STALE_SECONDS = 300

# Constants WordPress defines, in seconds.
WP_CONSTANTS = {
    'MINUTE_IN_SECONDS': 60,
    'HOUR_IN_SECONDS': 3600,
    'DAY_IN_SECONDS': 86400,
    'WEEK_IN_SECONDS': 604800,
    'MONTH_IN_SECONDS': 2592000,
    'YEAR_IN_SECONDS': 31536000,
}

root = Path(__file__).resolve().parent.parent
failures = []

for php in sorted(root.rglob('*.php')):
    if '/docs/' in php.as_posix():
        continue

    text = php.read_text(encoding='utf-8', errors='replace')

    if 'stale-while-revalidate' not in text:
        continue

    # The value is usually a sprintf placeholder filled from a later argument,
    # so look at the whole header call rather than the format string alone.
    for match in re.finditer(r'stale-while-revalidate=(\d+|%d)', text):
        tail = text[match.end():match.end() + 400]

        if match.group(1) != '%d':
            value = int(match.group(1))
        else:
            names = re.findall(r'\b([A-Z]+_IN_SECONDS)\b', tail)
            literals = re.findall(r'^\s*(\d+)\s*[,)]', tail, re.M)

            if names:
                value = WP_CONSTANTS.get(names[0], 0)
            elif literals:
                value = int(literals[0])
            else:
                continue

        if value > MAX_STALE_SECONDS:
            line = text[:match.start()].count('\n') + 1
            failures.append(
                '%s:%d  stale-while-revalidate resolves to %ds, over the %ds limit'
                % (php.relative_to(root).as_posix(), line, value, MAX_STALE_SECONDS)
            )

if failures:
    print('FAIL  HTML may be served stale for too long after a deploy')
    for f in failures:
        print('      ' + f)
    print()
    print('      A structural change will be invisible to returning visitors')
    print('      for that long, while the CSS on the page updates normally.')
    sys.exit(1)

print('ok   HTML stale window within %ds' % MAX_STALE_SECONDS)
