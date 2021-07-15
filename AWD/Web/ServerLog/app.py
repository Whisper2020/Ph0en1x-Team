import re

import requests
from flask import *

import utils

app = Flask(__name__)


@app.route('/')
def index():
    ip = request.args.get('ip', '')
    if len(ip) == 0:
        return render_template('index.html', title='Index')
    else:
        return render_template('server.html', title=ip, ip=ip)


@app.route('/install/', methods=['POST'])
def install():
    ip = request.form.get('ip', '')
    addr = f'http://{ip}/logger.php'
    return requests.get(addr, params={'pass': utils.password, 'cmd': utils.install}).text


@app.route('/uninstall/', methods=['POST'])
def uninstall():
    ip = request.form.get('ip', '')
    addr = f'http://{ip}/logger.php'
    return requests.get(addr, params={'pass': utils.password, 'cmd': utils.uninstall}).text


@app.route('/log/')
def log():
    ip = request.args.get('ip', '')
    addr = f'http://{ip}/logger.php'
    log = requests.get(addr, params={'pass': utils.password, 'cmd': utils.log}).text.strip()
    log = '\n\n'.join(sorted(re.split(r'\n\n(?=\[\d\d:\d\d:\d\d] )', log), reverse=True))
    log_filtered = requests.get(addr, params={'pass': utils.password, 'cmd': utils.log_filtered}).text.strip()
    log_filtered = '\n\n'.join(sorted(re.split(r'\n\n(?=\[\d\d:\d\d:\d\d] )', log_filtered), reverse=True))
    log_attack = requests.get(addr, params={'pass': utils.password, 'cmd': utils.log_attack}).text.strip()
    log_attack = '\n\n----------------------------------------------------\n\n'.join(
        sorted([i.strip() for i in log_attack.split('----------------------------------------')], reverse=True))
    return jsonify([log, log_filtered, log_attack])


@app.route('/shell/', methods=['POST'])
def shell():
    ip = request.args.get('ip', '')
    cmd = request.form.get('cmd', '')
    addr = f'http://{ip}/logger.php'
    return requests.get(addr, params={'pass': utils.password, 'cmd': cmd}).text


@app.route('/monitor/')
def monitor():
    ip = request.args.get('ip', '')
    addr = f'http://{ip}/logger.php'
    return requests.get(addr, params={'pass': utils.password, 'cmd': utils.monitor}).text


if __name__ == '__main__':
    app.run()
