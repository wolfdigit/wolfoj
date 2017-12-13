#!/usr/bin/env python

import subprocess
import sys
import fcntl
import os
import time

p = subprocess.Popen(os.path.dirname(os.path.realpath(__file__))+"/run.sh "+sys.argv[1], shell = True, stdin = subprocess.PIPE, stdout = subprocess.PIPE, stderr = sys.stderr)

flags = fcntl.fcntl(p.stdout, fcntl.F_GETFL)
flags = flags | os.O_NONBLOCK
fcntl.fcntl(p.stdout, fcntl.F_SETFL, flags)

def tryRead(pipe):
	try:
		time.sleep(0.03)
		retv = pipe.read()
	except:
		retv = "[]"
	return retv

sys.stdout.write(tryRead(p.stdout))
for line in sys.stdin.readlines():
	sys.stdout.write(line)
	p.stdin.write(line)
	sys.stdout.write(tryRead(p.stdout))

sys.stdout.write(tryRead(p.stdout))
