#!/usr/bin/env python3
"""Structural sanity check for PHP files when no PHP binary is available.

This is NOT a parser and it does not replace bin/lint-php.sh. It isolates the
PHP code regions, strips comments, strings and heredocs, then checks that
braces, parens and brackets balance and that every file opens with <?php.

That catches the errors that actually happen when generating PHP: an unclosed
brace, a stray quote, a heredoc whose terminator drifted. Install PHP and run
bin/lint-php.sh for the real check; CI runs it on every push.

Usage: python bin/php-structure-check.py [files...]   (default: all theme PHP)
"""
import re
import sys
import glob
import io

BACKSLASH = chr(92)
SQUOTE = chr(39)
DQUOTE = chr(34)
NEWLINE = chr(10)
HEREDOC_OPEN = chr(60) * 3


def code_regions(src):
    """Return only the parts of the file that are PHP code.

    A theme file flips between PHP and raw output many times, and the HTML and
    CSS in the output halves are full of braces that are just text. Checking
    those is what produced a wall of false positives on the widget files.
    """
    out = []
    i = 0
    while True:
        opens = [p for p in (src.find('<?php', i), src.find('<?=', i)) if p >= 0]
        if not opens:
            break
        o = min(opens)
        c = close_tag(src, o)
        if c < 0:
            out.append(src[o:])
            break
        out.append(src[o:c])
        i = c + 2
    return NEWLINE.join(out)


def close_tag(src, start):
    """Find the ?> that ends this code region, ignoring ones inside strings.

    inc/sitemap.php builds its XML with '<?xml version="1.0" ... ?>' in a
    single-quoted string. A naive search treats that as the end of PHP mode and
    silently drops the rest of the file, which reported as an unclosed brace.
    """
    j = start
    n = len(src)
    while j < n:
        ch = src[j]
        two = src[j:j + 2]
        # Comments first. An apostrophe in "doesn't" inside a // comment reads
        # as a string open otherwise, and the scan runs past the real ?>.
        if two == '//' or ch == '#':
            k = src.find(NEWLINE, j)
            j = n if k < 0 else k
            continue
        if two == '/*':
            k = src.find('*/', j + 2)
            j = n if k < 0 else k + 2
            continue
        if ch == DQUOTE or ch == SQUOTE:
            quote = ch
            j += 1
            while j < n:
                if src[j] == BACKSLASH:
                    j += 2
                    continue
                if src[j] == quote:
                    break
                j += 1
        elif src[j:j + 2] == '?>':
            return j
        j += 1
    return -1


def strip(src):
    """Remove comments, strings and heredocs so only structure remains."""
    out = []
    i = 0
    n = len(src)
    heredoc_re = re.compile(
        HEREDOC_OPEN + r"[ \t]*([" + SQUOTE + DQUOTE + r"]?)(\w+)\1\r?" + BACKSLASH + "n"
    )
    while i < n:
        c = src[i]
        two = src[i:i + 2]
        if two == '//' or c == '#':
            j = src.find(NEWLINE, i)
            i = n if j < 0 else j
        elif two == '/*':
            j = src.find('*/', i + 2)
            i = n if j < 0 else j + 2
        elif c == DQUOTE or c == SQUOTE:
            quote = c
            j = i + 1
            while j < n:
                if src[j] == BACKSLASH:
                    j += 2
                    continue
                if src[j] == quote:
                    j += 1
                    break
                j += 1
            i = j
        elif src[i:i + 3] == HEREDOC_OPEN:
            m = heredoc_re.match(src[i:])
            if m:
                tag = m.group(2)
                end = re.search(r"^\s*" + tag + r"\s*[;,)]?\s*$", src[i:], re.M)
                i = n if not end else i + end.end()
            else:
                out.append(c)
                i += 1
        else:
            out.append(c)
            i += 1
    return ''.join(out)


PAIRS = {')': '(', '}': '{', ']': '['}


def check(path):
    src = io.open(path, encoding='utf-8', errors='replace').read()
    errs = []
    if not src.lstrip().startswith('<?php'):
        errs.append('does not open with <?php')
    stack = []
    for ch in strip(code_regions(src)):
        if ch in '({[':
            stack.append(ch)
        elif ch in ')}]':
            if not stack or stack.pop() != PAIRS[ch]:
                errs.append('unbalanced ' + ch)
                break
    if stack and not any('unbalanced' in e for e in errs):
        errs.append('%d unclosed %s' % (len(stack), ''.join(stack[-6:])))
    return errs


def main():
    files = sys.argv[1:]
    if not files:
        files = sorted(
            glob.glob('*.php')
            + glob.glob('inc/**/*.php', recursive=True)
            + glob.glob('template-parts/**/*.php', recursive=True)
            + glob.glob('templates/**/*.php', recursive=True)
        )
    bad = 0
    for f in files:
        errs = check(f)
        if errs:
            bad += 1
            print('FAIL %-52s %s' % (f, '; '.join(errs)))
    print('%s %d file(s) checked, %d failed'
          % ('FAIL' if bad else 'ok  ', len(files), bad))
    return 1 if bad else 0


if __name__ == '__main__':
    sys.exit(main())
