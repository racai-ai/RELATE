#!/bin/sh

cron &

/usr/sbin/apachectl -D FOREGROUND
