判题机开机启动
---------

> 以下内容均需要 root 权限。非 root 用户可使用 `sudo`。

### 1. Judge
 
将以下内容保存创建为 `judge.service`，保存为 `/etc/systemd/system/judge.service`

```
[Unit]
Description=Start judge
After=network.target mysql.service mariadb.service

[Service]
# 根据安装修改为对应的安装路径，你应该要能在该路径找到可执行文件 dispatcher
ExecStart=-/home/judge/lpszoj/judge/dispatcher -o
RemainAfterExit=yes
KillMode=control-group
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### 2. Polygon

将以下内容保存创建为 `polygon.service`，保存为 `/etc/systemd/system/polygon.service`

```
[Unit]
Description=Start polygon
After=network.target mysql.service mariadb.service

[Service]
# 根据安装修改为对应的安装路径，你应该要能在该路径找到可执行文件 polygon
ExecStart=-/home/judge/lpszoj/polygon/polygon
RemainAfterExit=yes
KillMode=control-group
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### 3. 执行命令
```
systemctl daemon-reload
systemctl enable judge
systemctl enable polygon
```
