#!/usr/bin/python3

import sys
import os
import time
import pickle

base_path = os.path.abspath(os.path.dirname(__file__))

hour = time.localtime(time.time()).tm_hour
list = []
try:
  with open(base_path + "/smsqueue.dat", 'rb') as f:
    while True:
      list.append(pickle.load(f))

except:
  pass
  
if not len(list):
  sys.exit(-1)

with open(base_path + "/smsqueue.dat", 'wb') as f:
  for x in list:
    start_hour = x[0]
    end_hour = x[1]
    phone = x[2]
    message = x[3]
    
    if (start_hour <= hour) and (hour < end_hour) or (((start_hour <= hour) or (hour < end_hour)) and (start_hour >= end_hour)):
      import sender
      s = sender.Sender()
      print(s.send_sms(phone, message))
      
    else:
      pickle.dump(x, f)
