<!-- block=header -->
#!/bin/bash

# Определеяем общую ширину канала для инетрфейса eth0 в 1 Гигабит

tc qdisc del dev eth0 root
tc qdisc add dev eth0 root handle 1 htb default 1
tc class add dev eth0 parent 1: classid 1:1 htb rate 1000mbit
tc qdisc add dev eth0 parent 1:1 handle 3: sfq

<!-- block=host -->
# virtualHost {serverName},{port},{rate} -->

# Определяем ширину канала для виртуального FTP-хоста {serverName}

# Определяем класс для исходящего трафика с порта {port}
tc class add dev eth0 parent 1: classid 1:1{serverNum} htb rate {rate}kbit

# Направляем исходящий трафик с порта {port} в созданный канал
iptables -t mangle -F
iptables -t mangle -A OUTPUT -o eth0 -p tcp --sport 49152:65534 -j MARK --set-mark 19
iptables -t mangle -A OUTPUT -o eth0 -p tcp --sport 20 -j MARK --set-mark 19
tc filter add dev eth0 parent 1: protocol ip prio 1 handle 19 fw flowid 1:1{serverNum}

# Равномерно распределяем трафик внутри созданного канала
tc qdisc add dev eth0 parent 1:1{serverNum} handle 2{serverNum}: sfq

# <-- virtualHost {serverName},{port},{rate}
