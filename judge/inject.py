#!/usr/bin/env python

import sys
import re

lines = ''.join(sys.stdin.readlines())

newStr = '    setbuf(stdout, NULL);'
newlines = re.sub(r'(main\s*\([^\)]*\)\s*\{)', r'\1'+newStr, lines)
print newlines
