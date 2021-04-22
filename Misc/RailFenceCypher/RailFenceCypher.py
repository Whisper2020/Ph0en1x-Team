#! /usr/bin/env python3
# -*- encoding: utf-8 -*-
'''
@Author  : Wh1sper
@Desc    : RailFenceCypher decoder.
@Usage   : Set GroupSize for accurately decode or leave it None to calc and print all results.
'''

from math import ceil
from colorama import init, Fore, deinit

C = r"ccehgyaefnpeoobe{lcirg}epriec_ora_g" # Replace your string here. Use u"" to decode Chinese Character.
# C = u"Chinese Character"
GroupSize = None

# Settings for auto-HighLight. It detects KEYWORD and RegExpr /^.*{.*}$/
HighLight = True
KEYWORD = "flag"

'''
Core Function
'''
def Decrypt(cybertext: str, groupSize: int) -> str:
    assert groupSize > 0
    L = len(cybertext)
    gap_size = ceil(L / groupSize)
    Divisor = bool(L % groupSize == 0)
    M = []
    for _i in range(gap_size - int(not Divisor)):  # gap_size = group_num
        idx = _i
        for _j in range(groupSize):  # group_size = gap_num
            M.append(C[idx])
            idx += gap_size
            if not Divisor and _j >= L % groupSize:
                idx -= 1
    idx = gap_size - 1
    for _j in range(L % groupSize):
        M.append(C[idx])
        idx += gap_size
    return "".join(M)


if __name__ == '__main__':
    if HighLight:
        init(autoreset=True)
    print(f"Src =\t{C}\nLength = {len(C)}")
    if GroupSize is None:
        print("GroupSize\tPlainText")
        for i in range(len(C)):
            plain = Decrypt(C, i + 1)
            if HighLight and (plain.find(KEYWORD) != -1 or (plain[-1] == "}" and plain.find("{") != -1)):
                print(Fore.RED + f"{i + 1}\t{plain}")
            else:
                print(f"{i + 1}\t{plain}")
    else:
        print(Decrypt(C, GroupSize))
    if HighLight:
        deinit()
