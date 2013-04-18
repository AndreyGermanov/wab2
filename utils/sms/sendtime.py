#!/usr/bin/python3
# -*- coding: utf-8 -*-

import sys
import sender

if len(sys.argv) < 5:
  print('usage: ' + __file__ + "end_sleep_hour begin_sleep_hour number your message")
  sys.exit(-1)

try:
  start_hour = int(sys.argv[1])
  stop_hour = int(sys.argv[2])
  
except:
  print("end sleep hour and begin sleep hour must be integer")
  
if start_hour > 23 or stop_hour > 23:
  print("end sleep hour and begin sleep hour must be between 0 and 23")
  sys.exit(-1)
  
phone = sys.argv[3]

if phone[0] == "+":
  phone = phone[1:]
  
if not phone.isdigit():
  print("Invalid phone number")
  sys.exit(-1)

message = ""
for x in sys.argv[4:]:
  message += x + " "

message = message[:-1]
s = sender.Sender()

print(s.send_defer_sms(phone, message, start_hour, stop_hour))
