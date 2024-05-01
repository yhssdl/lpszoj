#!/usr/bin/env bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
#
# Auto install JNOJ
#
# Copyright (C) 2017-2019 Shiyang <dr@shiyang.me>
#
# System Required:  CentOS 6+, Debian7+, Ubuntu16+
#
# Reference URL:
# https://gitee.com/yhssdl/lpszoj
#

red='\033[0;31m'
green='\033[0;32m'
yellow='\033[0;33m'
plain='\033[0m'

[[ $EUID -ne 0 ]] && echo -e "[${red}Error${plain}] This script must be run as root!" && exit 1

disable_selinux(){
    if [ -s /etc/selinux/config ] && grep 'SELINUX=enforcing' /etc/selinux/config; then
        sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config
        setenforce 0
    fi
}

check_sys(){
    local checkType=$1
    local value=$2

    local release=''
    local systemPackage=''

    if [[ -f /etc/redhat-release ]]; then
        release="centos"
        systemPackage="yum"
    elif grep -Eqi "ubuntu|jammy|focal" /etc/issue; then
        release="ubuntu"
        systemPackage="apt"
    elif grep -Eqi "debian|raspbian|armbian" /etc/issue; then
        release="debian"
        systemPackage="apt"
    elif grep -Eqi "centos|red hat|redhat" /etc/issue; then
        release="centos"
        systemPackage="yum"
    elif grep -Eqi "Alpine" /etc/issue; then
        release="alpine"
        systemPackage="apk"
    elif grep -Eqi "ubuntu" /proc/version; then
        release="ubuntu"
        systemPackage="apt"            
    elif grep -Eqi "debian|raspbian|armbian" /proc/version; then
        release="debian"
        systemPackage="apt"
    elif grep -Eqi "centos|red hat|redhat" /proc/version; then
        release="centos"
        systemPackage="yum"
    fi

    if [[ "${checkType}" == "sysRelease" ]]; then
        if [ "${value}" == "${release}" ]; then
            return 0
        else
            return 1
        fi
    elif [[ "${checkType}" == "packageManager" ]]; then
        if [ "${value}" == "${systemPackage}" ]; then
            return 0
        else
            return 1
        fi
    fi
}

getversion(){
    if [[ -s /etc/redhat-release ]]; then
        grep -oE  "[0-9.]+" /etc/redhat-release
    else
        grep -oE  "[0-9.]+" /etc/issue
    fi
}

centosversion(){
    if check_sys sysRelease centos; then
        local code=$1
        local version="$(getversion)"
        local main_ver=${version%%.*}
        if [ "$main_ver" == "$code" ]; then
            return 0
        else
            return 1
        fi
    else
        return 1
    fi
}

debianversion(){
    if check_sys sysRelease debian;then
        local version=$( get_opsy )
        local code=${1}
        local main_ver=$( echo ${version} | sed 's/[^0-9]//g')
        if [ "${main_ver}" == "${code}" ];then
            return 0
        else
            return 1
        fi
    else
        return 1
    fi
}

error_detect_depends(){
    local command=$1
    local depend=`echo "${command}" | awk '{print $4}'`
    echo -e "[${green}Info${plain}] Starting to install package ${depend}"
    ${command}
    if [ $? -ne 0 ]; then
        echo -e "[${red}Error${plain}] Failed to install ${red}${depend}${plain}"
        echo "Please visit: https://gitee.com/yhssdl/lpszoj/wikis and contact."
        exit 1
    fi
}


install_dependencies(){
    if check_sys packageManager yum; then
        local version="$(getversion)"
        local main_ver=${version%%.*}
        echo -e "[${green}Info${plain}] Checking the EPEL repository..."
        yum install -y epel-release
        yum install -y http://rpms.remirepo.net/enterprise/remi-release-${main_ver}.rpm
   
        [ ! -f /etc/yum.repos.d/epel.repo ] && echo -e "[${red}Error${plain}] Install EPEL repository failed, please check it." && exit 1
        [ ! "$(command -v yum-config-manager)" ] && yum install -y yum-utils > /dev/null 2>&1
        [ x"$(yum-config-manager epel | grep -w enabled | awk '{print $3}')" != x"True" ] && yum-config-manager --enable epel > /dev/null 2>&1

        if [ "$main_ver" == "9" ]; then
            yum-config-manager --enable crb > /dev/null 2>&1
        elif [ "$main_ver" == "8" ]; then
            yum-config-manager --enable powertools > /dev/null 2>&1
            yum-config-manager --enable PowerTools > /dev/null 2>&1
        fi

        yum_depends=(
            nginx
            php74-php-cli php74-php-fpm php74-php-gd php74-php-mbstring php74-php-mysqlnd php74-php-xml php74-php-opcache
            mariadb mariadb-devel mariadb-server
            gcc-c++ glibc-static libstdc++-static git make gcc
            java-1.8.0-openjdk java-1.8.0-openjdk-devel
           
        )
        for depend in ${yum_depends[@]}; do
            error_detect_depends "yum -y install ${depend}"
        done


        if [ "$main_ver" == "9" ]; then
             error_detect_depends "yum -y install python3.11" 
             ln -s /usr/bin/python3.11 /usr/bin/python3 > /dev/null 2>&1
             error_detect_depends "yum -y install mariadb-connector-c-devel"
             error_detect_depends "yum -y install net-tools"

        elif [ "$main_ver" == "8" ]; then
             error_detect_depends "yum -y install python38" 
             ln -s /usr/bin/python3.8 /usr/bin/python3 > /dev/null 2>&1
             error_detect_depends "yum -y install mariadb-devel"
        else
             error_detect_depends "yum -y install python36" 
             ln -s /usr/bin/python3.6 /usr/bin/python3 > /dev/null 2>&1
             error_detect_depends "yum -y install mariadb-devel"
        fi

       
        ln -s /opt/remi/php74/root/usr/bin/php /usr/bin/php > /dev/null 2>&1
    elif check_sys sysRelease debian; then
        apt_depends=(
            nginx 
            php-mysql php-common php-gd php-zip php-xml php-mbstring php-fileinfo php-opcache php-fpm
            mariadb-server libmariadb-dev-compat libmariadb-dev
            git make gcc g++
            default-jdk
            net-tools
        )

        apt -y update
        for depend in ${apt_depends[@]}; do
            error_detect_depends "apt -y install ${depend}"
        done
    elif check_sys sysRelease alpine; then
        apt_depends=(
            nginx 
            php82 php82-common php82-gd php82-zip php82-xml php82-simplexml php82-mbstring php82-fileinfo php82-opcache php82-fpm php82-pdo php82-pdo_mysql php82-ctype php82-session php82-zip
            mariadb mariadb-client mariadb-dev 
            git make gcc g++ python3 openjdk8
            net-tools
        )

        apk update
        for depend in ${apt_depends[@]}; do
            error_detect_depends "apk add ${depend}"
        done
    elif check_sys packageManager apt; then   
        apt -y update
        apt_depends=(
            nginx
            php-mysql php-common php-gd php-zip php-xml php-mbstring php-fileinfo php-opcache php-fpm
            mysql-server libmysqlclient-dev libmysql++-dev
            git make gcc g++
            default-jdk
            net-tools
        )
        for depend in ${apt_depends[@]}; do
            error_detect_depends "apt -y install ${depend}"
        done
    fi
}

install_check(){
    if (! check_sys packageManager yum && ! check_sys packageManager apt && ! check_sys packageManager apk) || centosversion 5; then
        echo -e "[${red}Error${plain}] Your OS is not supported to run it!"
        echo "Please change to Debian 10+/Ubuntu 20+ and try again."
        exit 1
    fi
}




config_lpszoj(){
	key="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
	pass=""
	for i in {1..12}
	do
	  num=$[RANDOM%${#key}]
	  tmp=${key:num:1}
	  pass=${pass}${tmp}
	done

    DBNAME="ojdate"
    DBUSER="root"
    DBPASS=$pass
    PHP_VERSION=`php -v>&1|awk 'NR==1{print}'|awk -F ' ' '{print $2}'|awk -F '.' '{printf "%s.%s\n", $1, $2}'`

    if check_sys sysRelease centos; then
        mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.back
        cat>/etc/nginx/conf.d/lpszoj.conf<<EOF
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        root /var/www/lpszoj/web;
        index index.php;
        server_name _;
        client_max_body_size    128M;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }
        location ~ \.php$ {
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                fastcgi_pass unix:/var/opt/remi/php74/run/php-fpm/www.sock;
        }
}
EOF
        DBUSER="root"
        systemctl start mariadb
        mysqladmin -u root password $DBPASS
        sed -i "s/post_max_size = 8M/post_max_size = 128M/g" /etc/opt/remi/php74/php.ini
        sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 128M/g" /etc/opt/remi/php74/php.ini
        sed -i "s/= apache/= nginx/g" /etc/opt/remi/php74/php-fpm.d/www.conf
        sed -i "s/;listen.mode = 0660/listen.mode = 0666/g" /etc/opt/remi/php74/php-fpm.d/www.conf
        sed -i "s/127.0.0.1:9000/\\/var\\/opt\\/remi\\/php74\\/run\\/php-fpm\\/www.sock/g" /etc/opt/remi/php74/php-fpm.d/www.conf
        sed -i "s/80 default/800 default/g" /etc/nginx/nginx.conf
        chmod 755 /var/www
        chown nginx -R /var/www/lpszoj
    elif check_sys sysRelease debian; then
        mv /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.back
        cat>/etc/nginx/conf.d/lpszoj.conf<<EOF
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        root /var/www/lpszoj/web;
        index index.php;
        server_name _;
        client_max_body_size    128M;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        }
}
EOF
        DBUSER="root"
        systemctl start mariadb
        mysqladmin -u root password $DBPASS
        rm /etc/nginx/sites-enabled/default
        sed -i "s/post_max_size = 8M/post_max_size = 128M/g" /etc/php/${PHP_VERSION}/fpm/php.ini
        sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 128M/g" /etc/php/${PHP_VERSION}/fpm/php.ini
        systemctl restart nginx
        systemctl restart php${PHP_VERSION}-fpm
    elif check_sys sysRelease alpine; then
        PHP_VERSION=`php -v>&1|awk 'NR==1{print}'|awk -F ' ' '{print $2}'|awk -F '.' '{printf "%s%s\n", $1, $2}'`
        mv /etc/nginx/http.d/default.conf /etc/nginx/http.d/default.back
        cat>/etc/nginx/http.d/lpszoj.conf<<EOF
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        root /var/www/lpszoj/web;
        index index.php;
        server_name _;
        client_max_body_size    128M;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }
        location ~ \.php$ {
                include fastcgi.conf;
                fastcgi_index     index.php;
                fastcgi_pass 127.0.0.1:9000;
        }
}
EOF
        cat>/etc/my.cnf<<EOF
[client-server]
port		= 3306

[mysqld]
port		= 3306
default_storage_engine = InnoDB
EOF
        DBUSER="root"
        /etc/init.d/mariadb setup
        rc-service mariadb start
        ln -s /usr/bin/php82 /usr/bin/php
        mysqladmin -u root password $DBPASS
        sed -i "s/post_max_size = 8M/post_max_size = 128M/g" /etc/php${PHP_VERSION}/php.ini
        sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 128M/g" /etc/php${PHP_VERSION}/php.ini
        rc-service nginx start
    else
        cat>/etc/nginx/conf.d/lpszoj.conf<<EOF
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        root /var/www/lpszoj/web;
        index index.php;
        server_name _;
        client_max_body_size    128M;
        location / {
                try_files \$uri \$uri/ /index.php?\$args;
        }
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        }
}
EOF
        rm /etc/nginx/sites-enabled/default
        DBUSER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'`
        DBPASS=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'`
        sed -i "s/post_max_size = 8M/post_max_size = 128M/g" /etc/php/${PHP_VERSION}/fpm/php.ini
        sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 128M/g" /etc/php/${PHP_VERSION}/fpm/php.ini
        systemctl restart nginx
        systemctl restart php${PHP_VERSION}-fpm
    fi

    if check_sys sysRelease alpine; then
        cat>/etc/init.d/judge<<EOF
#!/sbin/openrc-run

name="judge"
command="/var/www/lpszoj/judge/dispatcher"
command_args="-o"
#command_background="yes"
 
depend() {
	after networking mariadb
}
EOF
        cat>/etc/init.d/polygon<<EOF
#!/sbin/openrc-run

name="polygon"
command="/var/www/lpszoj/polygon/polygon"
#command_background="yes"
 
depend() {
	after networking mariadb
}
EOF
        chmod +x /etc/init.d/judge
        chmod +x /etc/init.d/polygon
    else
        cat>/etc/systemd/system/judge.service<<EOF
[Unit]
Description=Judge
After=network.target mysql.service mariadb.service

[Service]
ExecStart=-/var/www/lpszoj/judge/dispatcher -o
ExecStop=/bin/pkill -9 dispatcher
RemainAfterExit=yes
KillMode=control-group
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF
        cat>/etc/systemd/system/polygon.service<<EOF
[Unit]
Description=Polygon
After=network.target mysql.service mariadb.service

[Service]
ExecStart=-/var/www/lpszoj/polygon/polygon
ExecStop=/bin/pkill -9 polygon
RemainAfterExit=yes
KillMode=control-group
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF
    fi


    mysql -h localhost -u$DBUSER -p$DBPASS -e "create database ojdate;"
    if [ $? -eq 0 ]; then
        # Modify database information
        sed -i "s/'username' => 'ojdate'/'username' => '$DBUSER'/g" /var/www/lpszoj/config/db.php
        sed -i "s/OJ_USER_NAME=ojdate/OJ_USER_NAME=$DBUSER'/g" /var/www/lpszoj/judge/config.ini
        sed -i "s/OJ_USER_NAME=ojdate/OJ_USER_NAME=$DBUSER'/g" /var/www/lpszoj/polygon/config.ini       
        sed -i "s/123456/$DBPASS/g" /var/www/lpszoj/config/db.php
        sed -i "s/123456/$DBPASS/g"  /var/www/lpszoj/judge/config.ini
        sed -i "s/123456/$DBPASS/g"  /var/www/lpszoj/polygon/config.ini

        sed -i "s/OJ_MYSQL_UNIX_PORT/#OJ_MYSQL_UNIX_PORT/g"  /var/www/lpszoj/judge/config.ini
        sed -i "s/OJ_MYSQL_UNIX_PORT/#OJ_MYSQL_UNIX_PORT/g"  /var/www/lpszoj/polygon/config.ini
    fi
}

config_firewall(){
    # open http/https services.
    firewall-cmd --zone=public --add-port=80/tcp --permanent
    firewall-cmd --zone=public --add-port=443/tcp --permanent 

    # reload firewall config
    firewall-cmd --reload
}

enable_server(){
    PHP_VERSION=`php -v>&1|awk 'NR==1{print}'|awk -F ' ' '{print $2}'|awk -F '.' '{printf "%s.%s\n", $1, $2}'`
    local version="$(getversion)"
    local main_ver=${version%%.*}
    if check_sys sysRelease centos; then
        systemctl start mariadb
        systemctl start php74-php-fpm
        systemctl enable php74-php-fpm
        systemctl enable mariadb
    elif check_sys sysRelease debian; then
        if [ "$PHP_VERSION" -ge "7.4" ]; then
            systemctl start php74-php-fpm
            systemctl enable php74-php-fpm
        else
            systemctl start php${PHP_VERSION}-fpm
            systemctl enable php${PHP_VERSION}-fpm
        fi
        systemctl start mariadb
        systemctl enable mariadb
        service mysql restart
    elif check_sys sysRelease alpine; then
        rc-service nginx restart
        rc-service mariadb restart
        rc-service php-fpm82 restart
    else
        systemctl enable php${PHP_VERSION}-fpm
        systemctl enable mysql
    fi

    if check_sys sysRelease alpine; then
        PHP_VERSION=`php -v>&1|awk 'NR==1{print}'|awk -F ' ' '{print $2}'|awk -F '.' '{printf "%s%s\n", $1, $2}'`
        rc-update add nginx
        rc-update add mariadb
        rc-update add php-fpm${PHP_VERSION}
        rc-update add judge
        rc-update add polygon
    else
        systemctl daemon-reload

        # startup service
        systemctl start nginx

        # startup service when booting.
        systemctl enable nginx    

        systemctl enable judge
        systemctl enable polygon
    fi
}

install_lpszoj(){
    disable_selinux
    install_check
    install_dependencies
    if check_sys packageManager apk; then
        adduser -D -u 1536 judge
    else
        /usr/sbin/useradd -m -u 1536 judge
    fi
    cd /var/www/
    git clone https://gitee.com/yhssdl/lpszoj.git

    config_lpszoj
    if check_sys packageManager yum; then
        config_firewall
    fi
    enable_server

    cd /var/www/lpszoj
    echo -e "yes" "\n" "admin" "\n" "123456" "\n" "admin@lpszoj.org" | ./yii install
    cd /var/www/lpszoj/judge
    make
    ./dispatcher -o
    cd /var/www/lpszoj/polygon
    make
    ./polygon
    echo
    echo
    echo -e "[${green}Mysql account${plain}] $DBUSER"
    echo -e "[${green}Password${plain}] $DBPASS"
    echo
    echo -e "[${green}Administrator account${plain}] admin"
    echo -e "[${green}Password${plain}] 123456"
    echo
    echo "Enjoy it!"
    echo "Welcome to visit: https://gitee.com/yhssdl"
    echo
    echo "Successful installation"
    echo    
    echo "App running at:"
    ip=`ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk '{print $2}'|tr -d "addr:"​`
    echo -e "${red}http://$ip${plain}" 
    echo
    echo

}

install_lpszoj
