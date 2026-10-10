<?php
// Pune Furnitures enquiry handler. Saves every enquiry to data/enquiries.csv (blocked from the web)
// and emails it if NOTIFY_EMAIL is set.
const NOTIFY_EMAIL = '';                       // e.g. 'info@yourdomain.com' (leave '' to only save)
const FROM_EMAIL   = 'noreply@yourdomain.com'; // must be a mailbox on your domain (Hostinger)
const MAX_PER_HOUR = 5;                        // per visitor IP

header('Content-Type: application/json; charset=utf-8');
function out(int $code, string $msg){ http_response_code($code); echo json_encode(['ok'=>$code<300,'message'=>$msg]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, 'Method not allowed');
if (!empty($_POST['website'])) out(200, 'OK'); // honeypot: bots fill this

$clean = fn($k,$n)=> trim(preg_replace('/[\x00-\x1F\x7F]+/u',' ', mb_substr((string)($_POST[$k] ?? ''),0,$n)));
$name=$clean('name',80); $phone=$clean('phone',20); $interest=$clean('interest',60);
$msg = trim(mb_substr(str_replace("\0",'',(string)($_POST['msg'] ?? '')),0,1500));
if (mb_strlen($name)<2 || !preg_match('/^[+\d][\d\s-]{8,14}$/',$phone)) out(422,'Invalid details');

$dir = __DIR__.'/data'; if(!is_dir($dir)) mkdir($dir,0750,true);
$ip = $_SERVER['REMOTE_ADDR'] ?? 'x'; $rf = $dir.'/rate.json';
$rate = is_file($rf) ? (json_decode(file_get_contents($rf),true) ?: []) : [];
$now=time(); $hits=array_filter($rate[$ip] ?? [], fn($t)=>$t>$now-3600);
if (count($hits)>=MAX_PER_HOUR) out(429,'Too many requests');
$hits[]=$now; $rate[$ip]=array_values($hits);
foreach($rate as $k=>$v){ if(!array_filter($v, fn($t)=>$t>$now-3600)) unset($rate[$k]); }
file_put_contents($rf, json_encode($rate), LOCK_EX);

$safe = fn($s)=> preg_match('/^[=+\-@]/',$s) ? "'".$s : $s; // block spreadsheet formula injection
$fh = fopen($dir.'/enquiries.csv','a'); if(!$fh) out(500,'Could not save');
flock($fh,LOCK_EX);
fputcsv($fh,[date('Y-m-d H:i:s'),$safe($name),$safe($phone),$safe($interest),$safe($msg),'New']);
flock($fh,LOCK_UN); fclose($fh);

if (NOTIFY_EMAIL !== '') {
  $body="Name: $name\nPhone: $phone\nInterested in: $interest\n\n$msg\n";
  @mail(NOTIFY_EMAIL,'New enquiry: '.$interest,$body,"From: ".FROM_EMAIL."\r\nContent-Type: text/plain; charset=UTF-8");
}
out(200,'Thank you');
