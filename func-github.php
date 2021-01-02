<?php


// get SHA existing
function fgh_get_existing_sha(string $file_name, string $harasia): string {
	$ch_git = curl_init("https://api.github.com/repos/trontonmogok/trontonmogok.github.io/contents/$file_name");
	curl_setopt($ch_git, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch_git, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch_git, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.3.314 Yowser/2.5 Safari/537.36',
		"Authorization: token $harasia"
	));
	$result_git=	curl_exec($ch_git);
	$info_git=		curl_getinfo($ch_git);
	$data_git=		json_decode($result_git);
	//print_rdie($data_git->sha);
	//print_rdie($info_git);

	$file_sha=	"";
	if ($info_git['http_code']==200 && !stristr($result_git, 'not found')) $file_sha= $data_git->sha;

	return $file_sha;
}


function fgh_upload_new_file(string $file_name, string $file_sha, string $file_txt, string $harasia): int {
	$data_git=	array(
		'sha'			=>	$file_sha,
		'message'	=>	"afile $file_today",
		'content'	=>	base64_encode($file_txt),
		'branch'	=>	'main',
		'path'		=>	$file_name,
	);
	$data_string_git=	json_encode($data_git);
	$ch_git = curl_init("https://api.github.com/repos/trontonmogok/trontonmogok.github.io/contents/$file_name");
	curl_setopt($ch_git, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch_git, CURLOPT_POSTFIELDS, $data_string_git);
	curl_setopt($ch_git, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch_git, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 YaBrowser/19.9.3.314 Yowser/2.5 Safari/537.36',
	"Authorization: token $harasia"
	));
	$result_git=	curl_exec($ch_git);
	$info_git=		curl_getinfo($ch_git);
	//echo $result_git;

	return intval($info_git['http_code']);
}
