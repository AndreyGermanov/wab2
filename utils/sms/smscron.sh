#!/bin/bash

BASEPATH=/opt/WAB2/utils/sms

FILENAME=$BASEPATH/smsqueue.dat
PATH=/usr/bin:/bin

if [ -s "$FILENAME" ]; then
$BASEPATH/smscron.py
fi
