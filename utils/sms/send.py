#!/usr/bin/python3
# -*- coding: utf-8 -*-

import sys
import sender

argc = len(sys.argv)

if argc < 3:
  print('usage: ' + __file__ + " 79161111111 your message")
  sys.exit(-1)
  
phone = sys.argv[1]
if phone[0] == "+":
  phone = phone[1:]
  
if not phone.isdigit():
  print("Invalid phone number")
  sys.exit(-1)

message = ""
for x in sys.argv[2:]:
  message += x + " "

message = message[:-1]

s = sender.Sender()
print(s.send_sms(phone, message))
