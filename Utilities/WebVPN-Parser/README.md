## 此脚本可以构造使用WebVPN访问任意地址的请求

### 用法：
Input Link: http://www.bing.com
> 如果不指定协议，默认使用HTTP

### 原理：
生成的WebVPN链接: [https://webvpn.xmu.edu.cn](https://webvpn.xmu.edu.cn) /protocol/{iv}{AES(key='wvpnisthebest!', mode=AES.MODE_CFB, IV=iv, segment_size=128).encrypt(host)}\[/path]
