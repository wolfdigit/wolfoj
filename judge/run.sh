#!/bin/bash

ulimit -m 65536
ulimit -v 65536
ulimit -t 2

stdbuf -i 0 -o 0 -e 0 ./$1
