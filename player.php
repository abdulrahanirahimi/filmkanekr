<?php
$video_id = isset($_GET['video_id']) ? trim($_GET['video_id']) : '';
$tmdb     = isset($_GET['tmdb'])     ? intval($_GET['tmdb'])     : 0;
$season   = isset($_GET['season'])   ? intval($_GET['season'])   : 0;
$episode  = isset($_GET['episode'])  ? intval($_GET['episode'])  : 0;

if (empty($video_id)) {
    http_response_code(400);
    die('Geen video_id opgegeven.');
}

$request_url = "https://getsuperembed.link/"
    . "?video_id=" . urlencode($video_id)
    . "&tmdb="     . $tmdb
    . "&season="   . $season
    . "&episode="  . $episode
    . "&player_bg_color=000000"
    . "&player_font_color=ffffff"
    . "&player_primary_color=E24B4A"
    . "&player_secondary_color=6900e0"
    . "&player_loader=1"
    . "&player_sources_toggle_type=2";

// Haal de embed-URL op via cURL of file_get_contents
$embed_url = '';
if (function_exists('curl_version')) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,            $request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,        10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT,      'Mozilla/5.0');
    $embed_url = curl_exec($curl);
    curl_close($curl);
} else {
    $embed_url = @file_get_contents($request_url);
}

$embed_url = trim($embed_url);

if (empty($embed_url)) {
    die('SuperEmbed server reageert niet. Probeer later opnieuw.');
}

// SuperEmbed geeft een URL terug — toon die in een volledige iframe-pagina
if (strpos($embed_url, 'https://') !== false) {
    // Stuur een volledige HTML-pagina terug met de embed erin
    echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  html, body { width:100%; height:100%; background:#000; overflow:hidden; }
  iframe { width:100%; height:100%; border:none; display:block; }
</style>
</head>
<body>
<iframe src="' . htmlspecialchars($embed_url) . '" allowfullscreen allow="fullscreen; autoplay; encrypted-media"></iframe>
</body>
</html>';
} else {
    // SuperEmbed geeft soms direct HTML terug
    echo $embed_url;
}
?>
