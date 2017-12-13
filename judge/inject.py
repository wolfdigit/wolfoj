#!/usr/bin/env python

import sys
import re

for line in sys.stdin.readlines():
	sys.stdout.write(line)
	match = re.search('main\s*\(\s*\)', line)
	#print match
	if match:
		sys.stdout.write("setbuf(stdout, NULL);"+"\n")
