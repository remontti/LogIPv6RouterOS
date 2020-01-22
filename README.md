# Como entregando IPv6+IPv4 através de PPPoE/DHCPv6 PD e registrando os logs em um banco de dados utilizando Mikrotik/RouterOS

https://blog.remontti.com.br/3931

<img src="https://blog.remontti.com.br/wp-content/uploads/2020/01/logs_ipv6_remontti.png">

On UP
<pre>{
  :global TOKEN "xxxxxxxxxxxxxxxx";
  :global URLUP "http://________________/log6.php";
  :global checkconnection "200.200.200.200";
  :local ii 0;
  :local tt 300; # Aguarda até 5min para tentar 
  while ( $ii < $tt && ([/ping $checkconnection count=1]=0) ) do={
    :put $ii
    :set $ii ($ii + 1)
    :delay delay-time=1s
    :log error "Awaiting connection ... $checkconnection";
  }
  :local localAddr $"local-address"
  :local remoteAddr $"remote-address"
  :local callerId $"caller-id"
  :local calledId $"called-id"
  :local interfaceName [/interface get $interface name]
  :local RemoteIPv6 [/ipv6 nd prefix get value-name=prefix [find interface=$interfaceName]]
  :local i 0;
  :local x 1;  
  :local t 60; # Segundos aguardando ipv6 ser configurado no cliente
  while ($i < $t && [ :len [/ipv6 dhcp-server binding find server=$interfaceName] ] < $x) do={
    :put $i
    :set $i ($i + 1)
    :delay delay-time=1s
  }
  if ($i = $t) do={
    :log warning message="UP: $user | $callerId | $calledId | $remoteAddr | $localAddr | $RemoteIPv6 | NULL"
    /tool fetch url="$URLUP" http-data="action=i&token=$TOKEN&user=$user&mac=$callerId&nas=$localAddr&service=$calledId&ipv4=$remoteAddr&remoteipv6=$RemoteIPv6" http-method=post
  } else={
    :local DHCPv6PD [/ipv6 dhcp-server binding get value-name=address [find server=$interfaceName]]
    :log warning message="UP: $user | $callerId | $calledId | $remoteAddr | $localAddr | $RemoteIPv6 | $DHCPv6PD"
    /tool fetch url="$URLUP" http-data="action=i&token=$TOKEN&user=$user&mac=$callerId&nas=$localAddr&service=$calledId&ipv4=$remoteAddr&remoteipv6=$RemoteIPv6&dhcpv6pd=$DHCPv6PD" http-method=post
  }
  file remove log6.php
}</pre>

On Down
<pre>{
  :global TOKEN "xxxxxxxxxxxxxxxx"
  :global URLDOWN "http://________________/log6.php"
  :local localAddr $"local-address"
  :local callerId $"caller-id"
  /tool fetch url="$URLDOWN" http-data="action=u&token=$TOKEN&user=$user&mac=$callerId&nas=$localAddr" http-method=post
  :log error message="DOWN: $user | $callerId | $localAddr"
  file remove log6.php
}</pre>
