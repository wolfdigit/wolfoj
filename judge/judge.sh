#!/bin/bash
HTTPBASE="http://localhost/OJ2"
RUNPATH="/tmp/judgeSandbox"


FILE="$1"
PROB="$2"
RID="$3"

rm -rf $RUNPATH
mkdir $RUNPATH
cd $RUNPATH

wget $HTTPBASE/api/code/$FILE -O $FILE
rm -rf a.out
if [ x`head $FILE | grep 'stdio'` != x'' ]; then
	$(dirname "$0")/inject.py < $FILE > real.cpp
	g++ real.cpp -o a.out > CE.txt 2>&1
else
	g++ $FILE -o a.out > CE.txt 2>&1
fi
RES=$?

if [ $RES -ne 0 ]; then
	echo CE
	exit 1
fi

#wget $HTTPBASE/api/prob.php?prob=$PROB -O $PROB.php
wget $HTTPBASE/api/data/$PROB/index -O $PROB.idx -q

RE=0
for i in `cat $PROB.idx`; do
	wget $HTTPBASE/api/data/$PROB/$i -O in.txt -q
	$(dirname "$0")/run.py a.out < in.txt > out.txt 2> err.txt
	RES=$?

	if [ $RES -ne 0 ]; then
		RE=1
	fi
	
	#wget $HTTPBASE/data/$PROB/$i.ans -O ans.txt
	#diff -y --width=30 out.txt ans.txt > res$i.txt
	#RES=$?
	#if [ $RES -ne 0 ]; then
	#	NOTAC=1
	#fi
	# TODO: upload res$i.txt

	curl -F "res=$RES" -F "in=$i" -F "out=@"$RUNPATH"/out.txt" -F "err=@"$RUNPATH"/err.txt" $HTTPBASE/api/upload/$RID
done

if [ $RE -eq 0 ]; then
	exit 4
else
	exit 2
fi
