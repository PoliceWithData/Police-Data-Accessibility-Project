<?php

//Using tor. Make sure you have ExitNode {us} in your torrc
$proxy = "127.0.0.1:9050";

$ch = curl_init();
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);

curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.111 Safari/537.36");
curl_setopt($ch, CURLOPT_HEADER, 0);

$f = fopen("../../Privacy_Public Access to Court Records State Links.csv", "r");
if ($f) {
    $fn = fopen("Privacy_Public Access to Court Records State Links Updated.csv", "w");
    $row = fgetcsv($f);
    fputcsv($fn, $row);
    $last_state = "";
    while (($row = fgetcsv($f)) !== FALSE) {
        if (!empty($row[2])) {
            if (!empty($row[0])) $last_state = $row[0];
            $dir = $last_state.'/'.$row[1];

            print("Checking $dir $row[2]\n");

            @mkdir($dir, 0777, true);
            curl_setopt($ch, CURLOPT_URL, $row[2]);
            $res = curl_exec($ch);
            file_put_contents($dir."/index.html", $res);
            $rcode = curl_getinfo($ch,  CURLINFO_RESPONSE_CODE);
            if ($rcode == 200) $row[3] = 'Y';
            else $row[3] = 'N';
        }
        fputcsv($fn, $row);
    }
    fclose($f);
    fclose($fn);
} else {
    print("Unable to open CSV file\n");
}
curl_close($ch);
