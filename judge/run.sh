#!/bin/bash

ulimit -m 65536
ulimit -v 65536
ulimit -t 2

./$1
