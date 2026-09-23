<?php
require __DIR__ . '/app/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
$base=rtrim(base_url(''),'/');
if($base===''){$scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$host=$_SERVER['HTTP_HOST']??'localhost';$base=$scheme.'://'.$host;}
$urls=[['/','1.0'],['/?lang=en','0.9'],['/qr-menu.php','0.8']];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach($urls as [$path,$priority]) echo '<url><loc>'.e($base.$path).'</loc><changefreq>weekly</changefreq><priority>'.$priority.'</priority></url>';
echo '</urlset>';
