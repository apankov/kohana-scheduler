#!/bin/bash

basedir=`dirname $0`
cd $basedir/../../../httpdocs
php index.php --uri=/cron/scheduler/run
