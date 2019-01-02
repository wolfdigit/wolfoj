#!/bin/bash

HTTPBASE="http://localhost/OJ2"
RUNPATH="/tmp/judgeSandbox"

export LANG=C
export LC_TIME=C
while true; do
	wget $HTTPBASE/api/job -O /tmp/job -q

	FILE=`head -1 /tmp/job`
	RID=`head -2 /tmp/job | tail -1`
	PROB=`tail -1 /tmp/job`

	if [ x"$RID" == x"" ]; then
		echo `date`: "no job is good job! waiting 30 secs ..."
		sleep 30
		continue
	fi

	$(dirname $(realpath "$0"))/judge.sh $FILE $PROB $RID
	RES=$?
	curl -F "prob=$PROB" -F "runid=$RID" -F "res=$RES" -F "ce=@"$RUNPATH"/CE.txt" $HTTPBASE/api/result/$PROB/$RID

	sleep 1
done
