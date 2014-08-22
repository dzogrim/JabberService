#!/bin/sh

# Initialize the firewall for explicitly allowed connexions only : 

echo "Initial Policy and flush"

iptables -P INPUT DROP
iptables -P OUTPUT DROP
iptables -P FORWARD DROP

iptables -t filter -F
iptables -t filter -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X

echo "(IPv6) Initial Policy and flush"

ip6tables -P INPUT DROP
ip6tables -P OUTPUT DROP
ip6tables -P FORWARD DROP

ip6tables -t filter -F
ip6tables -t filter -X

echo "Standard Matrix"

iptables -D INPUT -j IN_STANDARD 2>/dev/null
iptables -D OUTPUT -j OUT_STANDARD 2>/dev/null

iptables -F IN_STANDARD 2>/dev/null
iptables -F OUT_STANDARD 2>/dev/null
iptables -N IN_STANDARD
iptables -N OUT_STANDARD

# LOOPBACK
iptables -A IN_STANDARD -i lo -j ACCEPT
iptables -A OUT_STANDARD -o lo -j ACCEPT

for dns in 91.194.60.250 91.194.60.251
do
    iptables -A OUT_STANDARD -p udp --dport 53 -d $dns -j ACCEPT
    iptables -A IN_STANDARD -p udp --sport 53 -s $dns -j ACCEPT
    iptables -A OUT_STANDARD -p tcp --dport 53 -d $dns -j ACCEPT
    iptables -A IN_STANDARD -p tcp --sport 53 -s $dns -j ACCEPT
done

# UPGRADES DEBIAN HTTP
iptables -A IN_STANDARD -p tcp --sport 80 -s 91.194.60.112  -j ACCEPT
iptables -A OUT_STANDARD -p tcp --dport 80 -d 91.194.60.112  -j ACCEPT

# SSH BACKUPS & Octopuce
for ip in 91.194.60.8 91.194.61.192/27
do
    iptables -A IN_STANDARD -p tcp --dport 22 -s $ip  -j ACCEPT
    iptables -A OUT_STANDARD -p tcp --sport 22 -d $ip  -j ACCEPT
done
# BUG WITH "--limit" module on LXC, don't use it for now ...
iptables -A OUT_STANDARD -p icmp -j ACCEPT        
iptables -A IN_STANDARD -p icmp -j ACCEPT
# WHOIS out
iptables -A OUT_STANDARD -p tcp --dport 43  -j ACCEPT
iptables -A IN_STANDARD -p tcp --sport 43  -j ACCEPT
# On main chain, jump there : 
iptables -A INPUT -j IN_STANDARD
iptables -A OUTPUT -j OUT_STANDARD

echo "(IPv6) Standard Matrix"

ip6tables -D INPUT -j IN_STANDARD 2>/dev/null
ip6tables -D OUTPUT -j OUT_STANDARD 2>/dev/null

ip6tables -F IN_STANDARD 2>/dev/null
ip6tables -F OUT_STANDARD 2>/dev/null
ip6tables -N IN_STANDARD
ip6tables -N OUT_STANDARD

# LOOPBACK
ip6tables -A IN_STANDARD -i lo -j ACCEPT
ip6tables -A OUT_STANDARD -o lo -j ACCEPT
# UPGRADES DEBIAN HTTP
ip6tables -A IN_STANDARD -p tcp --sport 80 -s 2001:67c:288::112  -j ACCEPT
ip6tables -A OUT_STANDARD -p tcp --dport 80 -d 2001:67c:288::112  -j ACCEPT

for ip in 2001:67c:288::8 2001:67c:288:1::/64
do
    ip6tables -A IN_STANDARD -p tcp --dport 22 -s $ip  -j ACCEPT
    ip6tables -A OUT_STANDARD -p tcp --sport 22 -d $ip  -j ACCEPT
done

# ICMP : ACCEPT 
ip6tables -A IN_STANDARD -p icmpv6 -j ACCEPT
ip6tables -A OUT_STANDARD -p icmpv6 -j ACCEPT
# WHOIS out
ip6tables -A OUT_STANDARD -p tcp --dport 43  -j ACCEPT
ip6tables -A IN_STANDARD -p tcp --sport 43  -j ACCEPT

# Multicast (for neighbor discovery)
ip6tables -A INPUT -s ff00::/8 -j ACCEPT
ip6tables -A OUTPUT -d ff00::/8 -j ACCEPT

# On main chain, jump there : 
ip6tables -A INPUT -j IN_STANDARD
ip6tables -A OUTPUT -j OUT_STANDARD

echo "Custom ports for Jabber, Https"

for inport in 5222 5269 443 25
do
    iptables -A INPUT -p tcp --dport $inport -j ACCEPT
    iptables -A OUTPUT -p tcp --sport $inport -j ACCEPT
    ip6tables -A INPUT -p tcp --dport $inport -j ACCEPT
    ip6tables -A OUTPUT -p tcp --sport $inport -j ACCEPT
done
for outport in 5269 25
do
    iptables -A INPUT -p tcp --sport $outport -j ACCEPT
    iptables -A OUTPUT -p tcp --dport $outport -j ACCEPT
    ip6tables -A INPUT -p tcp --sport $outport -j ACCEPT
    ip6tables -A OUTPUT -p tcp --dport $outport -j ACCEPT
done


echo "End main chain"

# Explicitly drop microsoft ports (no log)
for i in 137 138 139 445
do
    iptables -A INPUT -p tcp --dport $i -j DROP
    iptables -A INPUT -p udp --dport $i -j DROP
done
# we drop the broadcasted packets (local or global)
iptables -A INPUT -d 255.255.255.255 -j DROP
iptables -A INPUT -d 185.34.33.31 -j DROP
iptables -A INPUT -d 185.34.33.0 -j DROP
# also the multicast range
iptables -A INPUT -d 224.0.0.0/4 -j DROP

# don't use this: bug with Linux kernel <3.15 and LXC + Limit module on iptables !
#iptables -A INPUT -m limit --limit 2/second -j LOG --log-prefix "GENERIC INPUT "
#iptables -A OUTPUT -m limit --limit 2/second -j LOG --log-prefix "GENERIC OUTPUT "
iptables -A INPUT   -j LOG --log-prefix "GENERIC INPUT "
iptables -A OUTPUT  -j LOG --log-prefix "GENERIC OUTPUT "

echo "(IPv6) End main chain"
# we drop the broadcasted packets (local or global)
ip6tables -A INPUT -d ff02::1 -j DROP
ip6tables -A OUTPUT -d ff02::1 -j DROP
#ip6tables -A INPUT -m limit --limit 2/second -j LOG --log-prefix "GENERIC INPUT "
#ip6tables -A OUTPUT -m limit --limit 2/second -j LOG --log-prefix "GENERIC OUTPUT "
ip6tables -A INPUT -j LOG --log-prefix "GENERIC INPUT "
ip6tables -A OUTPUT -j LOG --log-prefix "GENERIC OUTPUT "

#echo "DEFAULT IS ACCEPT"
#iptables -A INPUT -j ACCEPT
#iptables -A OUTPUT -j ACCEPT
#ip6tables -A INPUT -j ACCEPT
#ip6tables -A OUTPUT -j ACCEPT
