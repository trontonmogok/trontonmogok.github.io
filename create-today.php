<?php

declare(strict_types=1);


global $dbh, $the_host, $argv;

@define('NODB', true);
include(dirname(__FILE__)."/_libs.php");
!is_null($dbh)?? @mysqli_close($dbh);

// check another instance
$basename=			escapeshellarg(basename(__FILE__));
$pid=						getmypid();
$basename_name=	str_ireplace('.php', '', $basename);

unset($outs);
@exec("ps auxw | grep -v grep | grep -v $pid | grep php | grep $basename | grep -v \"sh -c\" | wc -l", $outs);
$outs=	intval($outs[0]);
echo "---- check other $basename [$outs] \n";
if ($outs>0) die("too many instance of $basename ... \n\n");

$fname_today=	"bookmarks-" . strtolower(date('d-M-Y')) . ".html";
echo "$fname_today \n";

$str=		file_get_contents(ROOT_PATH . "/todays_urls");
$lines=	explode("\n", $str);

$tiles=			array();
$ggsearch=	array();
foreach ($lines as $aline){
	$aline=	trim($aline);
	if (empty($aline)) continue;

	list($aurl, $atit, $aimg)=	array_map('trim', explode('| === |', $aline));
	//print_rdie("$aurl, $atit, $aimg --- $aline");

	$arrs= parse_url($aurl);
	if (!isset($arrs['host'])) continue;
	if (empty($arrs['host'])) continue;
	if (is_valid_ip($arrs['host'])) continue;
	if (stristr($arrs['host'], '.now') || stristr($arrs['host'], '.loc') || stristr($arrs['host'], '.lo')) continue;

	if (stristr($aurl, 'steamboatid')) continue;
	if (stristr($aurl, 'tronton')) continue;
	if (stristr($aurl, 'embeumkm.com')) continue;
	if (stristr($aurl, 'gkjw.org')) continue;
	if (stristr($aurl, 'google.com/search')) {
		$ggsearch[]=	$aurl;
		continue;
	}
	//print_rdie($arrs);

	$adom= strtolower($arrs['host']);
	if (empty($aimg)) $aimg= "http://free.pagepeeker.com/v2/thumbs.php?size=x&amp;url=$adom";

	$aplate=<<<aaa
<li class="tile is-parent is-3">
	<article class="tile is-child box">
		<img src="$aimg" loading="lazy" alt="$adom" title="$adom" />
		<p class="title">$adom</p>
		<p class="subtitle"><a href="$aurl" target="_blank" title="$adom">$atit</a></p>
	</article>
</li>
aaa;

	$tiles[]=	$aplate;
}
$alltiles=	implode("\n", $tiles);

$plate_str= file_get_contents(ROOT_PATH . "/plates/bookmark.htm");
$plate_str= str_ireplace('{tpl_date}', date('d F Y'), $plate_str);
$plate_str= str_ireplace('{tpl_tiles}', $alltiles, $plate_str);


$gs_str=	"";

if (!empty($ggsearch)){
	$gs_li=	array();
	foreach ($ggsearch as $aurl){
		$arrs= parse_url($aurl);
		//print_rdie($arrs);
		if (!isset($arrs['query'])) continue;
		if (empty($arrs['query'])) continue;

		parse_str($arrs['query'], $vars);
		if (!isset($vars['q'])) continue;
		if (empty($vars['q'])) continue;
		//print_rdie($vars);

		$gs_q=		htmlentities($vars['q']);
		$gs_kw=		str_replace(' ', '+', $gs_q);
		$gs_url=	"https://www.google.com/search?newwindow=1&amp;q=$gs_kw";
		$gs_li[]=	<<<aaa
<li class="tile box is-3" title="$gs_q"><a href="$gs_url" title="$gs_q" target="_blank" ># $gs_q</a></li>
aaa;
	}
	$gs_str=	implode("\n", $gs_li);

	$gs_tpl=<<<aaa
<h3 class="title h3">Google Search</h3>
<ul class="tile is-ancestor gslinks">$gs_str</ul>
aaa;
	$gs_str=	$gs_tpl;
}
$plate_str= str_ireplace('{tpl_googlesearch}', $gs_str, $plate_str);




$files=	glob(ROOT_PATH . "/bookmarks-*.html");
//print_rdie($files);

$item=			0;
$inn_str=		"";
$max_item=	12;

if (!empty($files) && is_array($files)){
	$files=		dk_array_rand($files, 12);
	$fi_li=		array();

	foreach ($files as $afile){
		$file_base= basename($afile);
		$file_date= str_ireplace(array('bookmarks-', '.html'), '', $file_base);
		$file_date= str_ireplace('-', ' ', $file_date);
		$file_date= strtoupper($file_date);
		//print_rdie($file_date);

		if (!strcasecmp($fname_today, $file_base)) continue;

		$fi_li[]=	<<<aaa
<li class="tile box is-3" title="$file_date"><a href="$file_base" title="$file_date" target="_blank" >$file_date</a></li>
aaa;

		$item++;
		if ($item >= $max_item) break;
	}

	if (!empty($fi_li)){
		$fi_str=	implode("\n", $fi_li);

		$inn_tpl=<<<aaa
<h3 class="title h3 inners">Other Bookmark Pages</h3>
<ul class="tile is-ancestor gslinks">$fi_str</ul>
aaa;
		$inn_str=	$inn_tpl;
	}
}
$plate_str= str_ireplace('{tpl_innerpages}', $inn_str, $plate_str);

echo "NUM inner pages = $item \n\n";


file_put_contents($fname_today, $plate_str);
echo " --- " . humanize_filesize(filesize($fname_today)) . "\n\n";
