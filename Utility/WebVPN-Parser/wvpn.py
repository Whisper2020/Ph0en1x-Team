#! /usr/bin/env python3
# -*- encoding: utf-8 -*-
'''
@File    : wvpn.py
@Time    : 2021/05/11 周二 22:50:44
@Author  : Wh1sper
@Desc    : crack wvpn
'''

from math import ceil
import re
from binascii import b2a_hex
from Crypto.Cipher import AES
import webbrowser

key = b'wrdvpnisthebest!'
iv = b'Psst~psst~psst~~'
AEScipher = AES.new(key, mode=AES.MODE_CFB, IV=iv, segment_size=128)

raw_url=input("Input Link: ")
match = re.match(r'(?:(?P<protocol>\w*)://)?(?P<host>.+?)(?:(?:/)(?P<path>.*))?$', raw_url)
protocol = match.group('protocol')
if protocol == None:
	protocol = 'http'
host = match.group('host')
path = match.group('path')
print(f"Protocol: {protocol}\nHost: {host}\nPath: {path}")

_host = host.ljust(ceil(len(host) / AEScipher.block_size) * AEScipher.block_size, '\x00')
_cipher = AEScipher.encrypt(_host)
cipher = _cipher[:len(host)]

outlist = ["https://webvpn.xmu.edu.cn", protocol, bytes.decode(b2a_hex(iv) + b2a_hex(cipher))]
if path != None:
	outlist.append(path)
url = "/".join(outlist)
print(f"Link: {url}")
webbrowser.open(url)
