#!/usr/bin/env python3
"""Verify the five static-hero gates match between CSS and JS.

These five media queries decide who gets the scroll-scrubbed hero and who gets
the designed still. They are declared twice, once in assets/css/home.css and
once in assets/js/hero-scrub.js, and they must be character for character
identical in both.

When they drift, nothing throws. One side hides the stage while the other side
is still downloading a video nobody will see, or the CSS un-hides a stage the
JS never armed and the visitor gets a blank multi-viewport hero. Both failures
look like "the hero is broken on some phones" and are miserable to track down.

Run after touching either file.
"""
import io
import re
import sys

CSS = 'assets/css/home.css'
JS = 'assets/js/hero-scrub.js'


def js_gates():
    src = io.open(JS, encoding='utf-8').read()
    m = re.search(r"var GATES = \[(.*?)\];", src, re.S)
    if not m:
        return None
    return re.findall(r"'([^']+)'", m.group(1))


def css_gates():
    src = io.open(CSS, encoding='utf-8').read()
    m = re.search(r"@media \(max-width: 720px\),(.*?)\{", src, re.S)
    if not m:
        return None
    block = '(max-width: 720px),' + m.group(1)
    return [q.strip() for q in block.split(',') if q.strip()]


def main():
    a, b = js_gates(), css_gates()

    if a is None:
        print('FAIL could not find the GATES array in ' + JS)
        return 1
    if b is None:
        print('FAIL could not find the gate media query block in ' + CSS)
        return 1

    if a == b:
        print('ok   %d hero gates match between CSS and JS' % len(a))
        return 0

    print('FAIL hero gates differ between CSS and JS')
    for i in range(max(len(a), len(b))):
        x = a[i] if i < len(a) else '(missing)'
        y = b[i] if i < len(b) else '(missing)'
        if x != y:
            print('  %d:  JS  %s' % (i + 1, x))
            print('      CSS %s' % y)
    return 1


if __name__ == '__main__':
    sys.exit(main())
