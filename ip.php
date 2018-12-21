<?php
$query = urldecode(strtolower(trim($query)));

require_once('workflows.php');

$external = 'External';


$wf = new Workflows();

function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
}

function setResult($ip, $v4, $isExternal = false) {
    global $wf;
    global $external;
    if(strlen($ip) == 0) $ip = 'N/A';
    $sub = 'Copy '.$ip.' to clipboard!';
    $title = $isExternal ? $external.' ' : '';
    $title = $v4 ? $title.'IPv4' : $title.'IPv6';
    $title = $title.': '.$ip;
    $wf->result($ip, $ip, $title, $sub, 'icon.png');
}

if (strlen($query) != 0 && startsWith(strtolower($external), $query)) {
    $externalipv4=exec("curl -4 -s -m 5 https://ifconfig.co");
    $externalipv6=exec("curl -6 -s -m 5 https://ifconfig.co");
    setResult($externalipv4, true, true);
    setResult($externalipv6, false, true);
} else {
    $localipv4 = exec("ifconfig | grep 'inet.*broadcast' -m 1 | awk '{print $2}'");
    $localipv6 = exec("ifconfig | grep 'inet6.*%en' -m 1 | awk '{print $2}'");
    setResult($localipv4, true);
    setResult($localipv6, false);
}
echo $wf->toxml();
