#!/bin/sh

FN=`date +%Y%m%d%H%M%S`
mysqldump -u root -p wolfoj > $FN.sql
tar czf $FN-testdata.tgz /var/wolfoj/testdata
tar czf $FN-solutions.tgz /var/wolfoj/solutions
