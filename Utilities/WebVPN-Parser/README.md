## 此脚本能够构造使用WebVPN访问任意地址的请求
#### Python依赖：
> Python3

pip3 install pycrypto
### 用法：
直接运行脚本，输入访问地址

·python3 wvpn.py·

> 如果不指定协议，默认使用HTTP

### 原理：
生成的WebVPN链接: [https://webvpn.xmu.edu.cn](https://webvpn.xmu.edu.cn) /protocol/{iv}{AES(key='wvpnisthebest!', mode=AES.MODE_CFB, IV=iv, segment_size=128).encrypt(host)}\[/path]
