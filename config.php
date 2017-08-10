<?php

// CheckForSPAM Configuration File

// Site base URL
global $site_base_url;
//$site_base_url = "www.checkforspam.com/";  //live url
$site_base_url = "http://spam.loc/"; //local

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
    'AbuseIP' => "rbl.abuse.ro",
    'AbuseDomain' => "dbl.abuse.ro",
    'AnonmailsDNSBL' => "spam.dnsbl.anonmails.de",

//    'AntiCaptcha.NET IPv6' => "",
//    'BACKSCATTERER' => "ips.backscatterer.org",
//    'BARRACUDA' => "barracudacentral.org/rbl",
//    'BBFHL1' => "www.bbfh.org",
//    'BBFHL2' => "www.bbfh.org",

    'BLOCKLISTDE' => "all.bl.blocklist.de",
    'BSB' => "bsb.empty.us",

    'CALIVENT' => "dnsbl.calivent.com.pe",
    'CASA' => "cbl.anti-spam.org.cn",
    'CBL' => "cbl.abuseat.org",
    'CYMRUBOGONS' => "v4.fullbogons.cymru.com",
    'CYMRUBOGONSIPv6' => "v6.fullbogons.cymru.com",
    'DANTOR' => "tor.dan.me.uk",
    'DANTOREXIT' => "torexit.dan.me.uk",
    'DnsRealtimeBlackholeList' => 'dnsrbl.org',

//    'DMARC External Validation' => undefined,
//    'DMARC Multiple Records' => undefined,

    'DMARCRecordPublished' => "tools.ietf.org/html/rfc7489",

//    'DMARC Syntax Check' => undefined,
//    'DNS All Name Servers Timed Out' => undefined,

    'DNSAllServersAuthoritative' => "www.ietf.org/rfc/rfc1034.txt",

//    'DNS All Servers Responding' => undefined,

    'DNSAtLeastwoServers' => "tools.ietf.org/html/rfc2182",
    'DNSBadGlueDetected' => "tools.ietf.org/html/rfc1033",

//    'DNS Local Parent Mismatch' => undefined,
//    'DNS Lookup Timeout' => undefined,
//    'DNS Open Recursive Name Server' => undefined,
//    'DNS Open Zone Transfer' => undefined,

   // 'DNSPrimaryServerListedAtParent' => "mxtoolbox.com/problem/dns/dns-local-parent-mismatch",
    //'DNSRealtimeBlackholeList' => "dnsrbl.org/",

//    'DNS Record Published' => undefined,

    'DNSSERVICIOS' => "rbl.dns-servicios.com",
//    'DNSSOAExpireValue' => "mxtoolbox.com/problem/dns/dns-all-servers-authoritative",
//    'DNSSOANXDOMAINValue' => "tools.ietf.org/html/rfc2308",
//    'DNSSOARefreshValue' => "www.ietf.org/rfc/rfc1912.txt",
//    'DNSSOARetryValue' => "www.ietf.org/rfc/rfc1912.txt",

//    'DNS SOA Serial Number Format' => undefined,
//    'DNS SOA Serial Numbers Match' => undefined,
//    'DNS Server Allows Zone Transfer' => undefined,

//    'DNSServersHavePublicIPAddresses' => "en.wikipedia.org/wiki/Reserved_IP_addresses",
//    'DNSServersareonDifferentSubnets' => "tools.ietf.org/html/rfc2182",

//    'DOMAIN DNS Failure' => undefined,
//    'DOMAIN Monitors Change' => undefined,

    'DRMX' => " bl.drmx.org",
    //'DRONEBL' => "dnsbl.dronebl.org",
//    'DULRU' => "www.dul.ru/",
//    'Domain Expiration Check' => undefined,
    'FABELSOURCES' => "spamsources.fabel.dk",
//    'HIL' => "www.habeas.com/supportWhiteList.html",
//    'HIL2' => "www.habeas.com",
//    'HTTPConnect' => "mxtoolbox.com/problem/http/http-dns",
//    'HTTPDelay Check' => "mxtoolbox.com/problem/http/http-connect",
//    'HTTPDns' => "mxtoolbox.com/problem/http/http-connect",
//    'HTTPFilter' => "mxtoolbox.com/problem/http/http-connect",
//    'HTTPSCertificate Check' => "mxtoolbox.com/problem/https/https-certificate-expiration",
//    'HTTPSCertificate Expiration' => "mxtoolbox.com/Public/UpgradeV2.aspx",

    'IBMDNSBlacklist' => "dnsbl.cobion.com",
    'ICMFORBIDDEN' => "dnsrbl.swinog.ch",
//    'IMPSPAM' => "antispam.imp.ch/?lng=1",
//    'IMPWORM' => "antispam.imp.ch/?lng=1",
//    'INPS_DE' => "dnsbl.inps.de/index.cgi?lang=en&site=00001",
    'INTERSERVER' => "rbldata.interserver.net",
    'IPrangeRBLProject' => "rbl.realtimeblacklist.com",
    'JIPPG' => "dialup.blacklist.jippg.org",
    'KEMPTBL' => "dnsbl.kempt.net",
//    'KISA' => "www.kisarbl.or.kr/english/", dns does not know
    'Konstant' => "bl.konstant.no",
//    'LASHBACK' => "www.lashback.com/blacklist/",
    'LNSGBLOCK' => "spamguard.leadmon.net",
    'LNSGBULK' => "spamguard.leadmon.net",
    'LNSGMULTI' => "spamguard.leadmon.net",
    'LNSGOR' => "spamguard.leadmon.net",
    'LNSGSRC' => "spamguard.leadmon.net",
    'MADAVI' => "pop3.madavi.de",
    'MAILSPIKEBL' => "rep.mailspike.net",
    'MAILSPIKEZ' => "z.mailspike.net",
    'MSRBLPhishing' => "mx.fakemx.net",
    'MSRBLSpam' => "mx.fakemx.net",
//    'MailBlacklist' => "mailblacklist.com/",  dns does not know
//    'NETHERRELAYS' => "puck.nether.net", ????
//    'NETHERUNSURE' => "puck.nether.net", ???
    'NIXSPAM' => "ix.dnsbl.manitu.net",
    //  'NoSolicitado' => "www.nosolicitado.org/", ???
    'ORVEDB' => "rsbl.aupads.org",
    'OSPAM' => "0spam.fusionzero.com",
    'PSBL' => "psbl.surriel.com",
    'RATSDyna' => "dyna.spamrats.com",
    'RATSNoPtr' => "noptr.spamrats.com",
    'RATSSpam' => "spam.spamrats.com",
    'RBLJP' => "RBL.JP",
    'RSBL' => "rsbl.aupads.org",
    'SCHULTE' => "cbl.abuseat.org, etc",
    'SECTOOREXITNODES' => "www.sectoor.de/tor.php",
    'SEMBACKSCATTER' => "backscatter.spameatingmonkey.net",
    'SEMBLACK' => "backscatter.spameatingmonkey.net",

//    'SEM FRESH' => undefined,
//    'SEM URI' => undefined,

    //'SEMURIRED' => "mxtoolbox.com/Public/BlacklistDetails.aspx?bl=SEM-URI",
    'SERVICESNET' => "korea.services.net",

//    'SMTP Banner Check' => undefined,

    'SMTPConnect' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SMTPConnectionTime' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SMTPDNSResolution' => "mxtoolbox.com/DNSLookup.aspx",

//    'SMTP Open Relay' => undefined,

    'SMTPReverseDNSMismatch' => "mxtoolbox.com/supertool.aspx?action=a=>smtp.mxtoolbox.com",
    'SMTPReverseDNSResolution' => "mxtoolbox.com/ReverseLookup.aspx",

//    'SMTP Server Disconnected' => undefined,
//    'SMTP TLS' => undefined,

    'SMTPTransactionTime' => "technet.microsoft.com/en-us/library/bb123891.aspx#TF",
    'SORBSBLOCK' => "block.dnsbl.sorbs.net",
    'SORBSDUHL' => "dul.dnsbl.sorbs.net",
    'SORBSHTTP' => "http.dnsbl.sorbs.net",
    'SORBSMISC' => "misc.dnsbl.sorbs.net",
    'SORBSNEW' => "new.spam.dnsbl.sorbs.net",

//    'SORBS RHSBL BADCONF' => undefined,
//    'SORBS RHSBL NOMAIL' => undefined,

    'SORBSSMTP' => "smtp.dnsbl.sorbs.net",
    'SORBSSOCKS' => "socks.dnsbl.sorbs.net",
    'SORBSSPAM' => "spam.dnsbl.sorbs.net",
    'SORBSWEB' => "web.dnsbl.sorbs.net",
    'SORBSZOMBIE' => "zombie.dnsbl.sorbs.net",
//    'SPAMCANNIBAL' => "www.spamcannibal.org/",  dns does not know
   // 'SPAMCOP' => "bl.spamcop.net",
//    'SPEWS1' => "www.spews.org/", ???
//    'SPEWS2' => "www.spews.org/", ???
    'SuomispamReputation'=>'dbl.suomispam.net',
    //'SPF Included Lookups' => "tools.ietf.org/html/rfc7208",
    //'SPF Multiple Records' => "tools.ietf.org/html/rfc7208",
    //'SPF Record Deprecated' => "tools.ietf.org/html/rfc7208",



    'SWINOG' => "dnsrbl.swinog.ch",
//    'Sender Score Reputation Network' => "www.senderscore.org/landing/index.php?campid=701000000006WWq&redirect=/register", ???
    'SpamEatingMonkeySEMIPv6BL' => "uribl.spameatingmonkey.net",

    'SpamhausZEN' => "zen.spamhaus.org",


//    'TCP Connect' => undefined,

    //'TCP Delay Check' => "mxtoolbox.com/problem/http/tcp-connect",

//    'TCP Dns' => undefined,

    'TRIUMF' => "rbl2.triumf.ca",
    'TRUNCATE' => "truncate.gbudb.net",
    'UCEPROTECTL1' => "dnsbl-1.uceprotect.net ",
    'UCEPROTECTL2' => "dnsbl-2.uceprotect.net",
    'UCEPROTECTL3' => "dnsbl-3.uceprotect.net ",
    'VIRBL' => "virbl.dnsbl.bit.nl",
    'VirblIPv6' => "virbl.bit.nl",
    'WPBL' => "db.wpbl.info",
//    'Woodys SMTP Blacklist' => "blacklist.woody.ch/rblcheck.php3",   dns does not know
//    'Woodys SMTP Blacklist IPv6' => "blacklist.woody.ch/rblcheck.php3",   dns does not know
    'ZapBL' => "dnsbl.zapbl.net",
   'ivmSIP' => "dnsbl.invaluement.com/ivmsip/",
    'ivmSIP24' => "dnsbl.invaluement.com/ivmsip24/",

    //'ivmURI' => undefined,

    's5hnetIPv6' => "all.s5h.net",
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
$db_password = "123";
$db_name = "checkfor_main";

?>