<?php

// CheckForSPAM Configuration File

// Site base URL
global $site_base_url;
//$site_base_url = "www.checkforspam.com/";  //live url
$site_base_url = "http://checkforspam.loc/"; //local

// Spam servers
global $spam_blacklist_servers;
$spam_blacklist_servers = array(
    'Spamhaus' => 'zen.spamhaus.org',
    'SORBS' => 'dnsbl.sorbs.net',
    'DroneBL' => 'dnsbl.dronebl.org',
    'BarracudaCentral' => 'b.barracudacentral.org',
    'SpamCop' => 'bl.spamcop.net',
    'ManiTu' => 'ix.dnsbl.manitu.net',
    'AbuseAt' => 'cbl.abuseat.org',
    'ASPEWS' => "www.aspews.org",
    'Abuse.ro' => "abuse.ro/",
    'Anonmails DNSBL' => "www.anonmails.de/dnsbl.php",
    'AntiCaptcha.NET IPv6' => "anticaptcha.net/",
    'BACKSCATTERER' => "www.backscatterer.org/index.php",
    'BARRACUDA' => "barracudacentral.org/rbl",
    'BBFHL1' => "www.bbfh.org",
    'BBFHL2' => "www.bbfh.org",
    'BLOCKLIST.DE' => "www.blocklist.de/en/index.html",
    'BSB' => "bsb.spamlookup.net",

    'BSB Domain' => undefined,

    'CALIVENT' => "dnsbl.calivent.com.pe/",
    'CASA CBL' => "www.anti-spam.org.cn/",
    'CASA CDL' => "www.anti-spam.org.cn/",
    'CBL' => "cbl.abuseat.org/",
    'CYMRU BOGONS' => "www.team-cymru.org/Services/Bogons/",
    'CYMRU BOGONS IPv6' => "www.team-cymru.org/Services/Bogons/",
    'DAN TOR' => "www.dan.me.uk/dnsbl",
    'DAN TOREXIT' => "www.dan.me.uk/dnsbl",

    'DMARC External Validation' => undefined,
    'DMARC Multiple Records' => undefined,

    'DMARC Record Published' => "tools.ietf.org/html/rfc7489",

    'DMARC Syntax Check' => undefined,
    'DNS All Name Servers Timed Out' => undefined,

    'DNS All Servers Authoritative' => "www.ietf.org/rfc/rfc1034.txt",

    'DNS All Servers Responding' => undefined,

    'DNS At Least Two Servers' => "tools.ietf.org/html/rfc2182",
    'DNS Bad Glue Detected' => "tools.ietf.org/html/rfc1033",

    'DNS Local Parent Mismatch' => undefined,
    'DNS Lookup Timeout' => undefined,
    'DNS Open Recursive Name Server' => undefined,
    'DNS Open Zone Transfer' => undefined,

    'DNS Primary Server Listed At Parent' => "mxtoolbox.com/problem/dns/dns-local-parent-mismatch",
    'DNS Realtime Blackhole List' => "dnsrbl.org/",

    'DNS Record Published' => undefined,

    'DNS SERVICIOS' => "rbl.dns-servicios.com/rbl.php",
    'DNS SOA Expire Value' => "mxtoolbox.com/problem/dns/dns-all-servers-authoritative",
    'DNS SOA NXDOMAIN Value' => "tools.ietf.org/html/rfc2308",
    'DNS SOA Refresh Value' => "www.ietf.org/rfc/rfc1912.txt",
    'DNS SOA Retry Value' => "www.ietf.org/rfc/rfc1912.txt",

    'DNS SOA Serial Number Format' => undefined,
    'DNS SOA Serial Numbers Match' => undefined,
    'DNS Server Allows Zone Transfer' => undefined,

    'DNS Servers Have Public IP Addresses' => "en.wikipedia.org/wiki/Reserved_IP_addresses",
    'DNS Servers are on Different Subnets' => "tools.ietf.org/html/rfc2182",

    'DOMAIN DNS Failure' => undefined,
    'DOMAIN Monitors Change' => undefined,

    'DRMX' => "drmx.org",
    'DRONE BL' => "dronebl.org",
    'DULRU' => "www.dul.ru/",
    'Domain Expiration Check' => undefined,
    'FABELSOURCES' => "www.spamsources.fabel.dk/",
    'HIL' => "www.habeas.com/supportWhiteList.html",
    'HIL2' => "www.habeas.com",
    'HTTP Connect' => "mxtoolbox.com/problem/http/http-dns",
    'HTTP Delay Check' => "mxtoolbox.com/problem/http/http-connect",
    'HTTP Dns' => "mxtoolbox.com/problem/http/http-connect",
    'HTTP Filter' => "mxtoolbox.com/problem/http/http-connect",
    'HTTPS Certificate Check' => "mxtoolbox.com/problem/https/https-certificate-expiration",
    'HTTPS Certificate Expiration' => "mxtoolbox.com/Public/UpgradeV2.aspx",
    'IBM DNS Blacklist' => "www-01.ibm.com/support/docview.wss?uid=swg21436643",
    'ICMFORBIDDEN' => "sunsite.icm.edu.pl/spam/bh.html",
    'IMP SPAM' => "antispam.imp.ch/?lng=1",
    'IMP WORM' => "antispam.imp.ch/?lng=1",
    'INPS_DE' => "dnsbl.inps.de/index.cgi?lang=en&site=00001",
    'INTERSERVER' => "rbldata.interserver.net",
    'IPrange RBL Project' => "iprange.net/rbl/?",
    'JIPPG' => "blacklist.jippg.org/",
    'KEMPTBL' => "www.kempt.net/dnsbl/",
    'KISA' => "www.kisarbl.or.kr/english/",
    'Konstant' => "bl.konstant.no/",
    'LASHBACK' => "www.lashback.com/blacklist/",
    'LNSGBLOCK' => "www.leadmon.net/spamguard",
    'LNSGBULK' => "www.leadmon.net/spamguard",
    'LNSGMULTI' => "www.leadmon.net/spamguard",
    'LNSGOR' => "www.leadmon.net/spamguard",
    'LNSGSRC' => "www.leadmon.net/spamguard",
    'MADAVI' => "www.madavi.de/index.php?id=39&type=0",
    'MAILSPIKE BL' => "mailspike.net",
    'MAILSPIKE Z' => "mailspike.net",
    'MSRBL Phishing' => "msrbl.blogspot.com/",
    'MSRBL Spam' => "msrbl.blogspot.com/",
    'MailBlacklist' => "mailblacklist.com/",
    'NETHERRELAYS' => "puck.nether.net/or/",
    'NETHERUNSURE' => "puck.nether.net/or/",
    'NIXSPAM' => "www.heise.de/ix/NiX-Spam-DNSBL-and-blacklist-for-download-499637.html",
    'NoSolicitado' => "www.nosolicitado.org/",
    'ORVEDB' => "www.aupads.org/",
    'OSPAM' => "0spam.fusionzero.com/",
    'PSBL' => "psbl.org/",
    'RATS Dyna' => "www.spamrats.com",
    'RATS NoPtr' => "www.spamrats.com",
    'RATS Spam' => "www.spamrats.com",
    'RBL JP' => "www.rbl.jp/allrbl-e.html",
    'RSBL' => "www.aupads.org/",
    'SCHULTE' => "rbl.schulte.org/",
    'SECTOOR EXITNODES' => "www.sectoor.de/tor.php",
    'SEM BACKSCATTER' => "spameatingmonkey.com/index.html",
    'SEM BLACK' => "spameatingmonkey.com/index.html",

    'SEM FRESH' => undefined,
    'SEM URI' => undefined,

    'SEM URIRED' => "mxtoolbox.com/Public/BlacklistDetails.aspx?bl=SEM-URI",
    'SERVICESNET' => "korea.services.net/",

    'SMTP Banner Check' => undefined,

    'SMTP Connect' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SMTP Connection Time' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SMTP DNS Resolution' => "mxtoolbox.com/DNSLookup.aspx",

    'SMTP Open Relay' => undefined,

    'SMTP Reverse DNS Mismatch' => "mxtoolbox.com/supertool.aspx?action=a=>smtp.mxtoolbox.com",
    'SMTP Reverse DNS Resolution' => "mxtoolbox.com/ReverseLookup.aspx",

    'SMTP Server Disconnected' => undefined,
    'SMTP TLS' => undefined,

    'SMTP Transaction Time' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SORBS BLOCK' => "www.sorbs.net/lookup.shtml",
    'SORBS DUHL' => "www.sorbs.net/lookup.shtml",
    'SORBS HTTP' => "www.sorbs.net/lookup.shtml",
    'SORBS MISC' => "www.sorbs.net/lookup.shtml",
    'SORBS NEW' => "mxtoolbox.com/problem/blacklist/sorbs-spam",

    'SORBS RHSBL BADCONF' => undefined,
    'SORBS RHSBL NOMAIL' => undefined,

    'SORBS SMTP' => "www.sorbs.net/lookup.shtml",
    'SORBS SOCKS' => "www.sorbs.net/lookup.shtml",
    'SORBS SPAM' => "www.sorbs.net/lookup.shtml",
    'SORBS WEB' => "www.sorbs.net/lookup.shtml",
    'SORBS ZOMBIE' => "www.sorbs.net/lookup.shtml",
    'SPAMCANNIBAL' => "www.spamcannibal.org/",
    'SPAMCOP' => "spamcop.net/bl.shtml",
    'SPEWS1' => "www.spews.org/",
    'SPEWS2' => "www.spews.org/",
    'SPF Included Lookups' => "tools.ietf.org/html/rfc7208",
    'SPF Multiple Records' => "tools.ietf.org/html/rfc7208",
    'SPF Record Deprecated' => "tools.ietf.org/html/rfc7208",

    'SPF Record Published' => undefined,
    'SPF Syntax Check' => undefined,
    'SPF Test Result' => undefined,
    'SURBL multi' => undefined,

    'SWINOG' => "antispam.imp.ch/?lng=1",
    'Sender Score Reputation Network' => "www.senderscore.org/landing/index.php?campid=701000000006WWq&redirect=/register",
    'Spam Eating Monkey SEM IPv6BL' => "spameatingmonkey.com/",

    'Spamhaus DBL' => undefined,

    'Spamhaus ZEN' => "www.spamhaus.org",
    'Suomispam Reputation' => "suomispam.net/",

    'TCP Connect' => undefined,

    'TCP Delay Check' => "mxtoolbox.com/problem/http/tcp-connect",

    'TCP Dns' => undefined,

    'TRIUMF' => "trmail.triumf.ca/cgi-bin/rbl2/",
    'TRUNCATE' => "www.gbudb.com/truncate/index.jsp",
    'UCEPROTECTL1' => "www.uceprotect.net/",
    'UCEPROTECTL2' => "www.uceprotect.net/",
    'UCEPROTECTL3' => "www.uceprotect.net/",
    'VIRBL' => "virbl.bit.nl/",
    'Virbl IPv6' => "virbl.bit.nl/",
    'WPBL' => "www.wpbl.info/",
    'Woodys SMTP Blacklist' => "blacklist.woody.ch/rblcheck.php3",
    'Woodys SMTP Blacklist IPv6' => "blacklist.woody.ch/rblcheck.php3",
    'ZapBL' => "zapbl.net/",
    'ivmSIP' => "dnsbl.invaluement.com/ivmsip/",
    'ivmSIP24' => "dnsbl.invaluement.com/ivmsip24/",

    'ivmURI' => undefined,

    's5h.net IPv6' => "www.usenix.org.uk/content/rbl.html",
);


// MySQL Params
global $db_servername;
global $db_username;
global $db_password;
global $db_name;
$db_servername = "localhost";
//live db access
//$db_username = "checkfor_main";
//$db_password = "mk#w9%ww??r~";
// local db access
$db_username = "root";
$db_password = "123123";
$db_name = "checkfor_main";

?>