<?php
$fname = 'tmp2channels.wav';
$request = file_get_contents('php://input');
$fp = fopen($fname, 'w');
fwrite($fp, $request);
fclose($fp);
exec('rm speech.wav');
exec('ffmpeg -i tmp2channels.wav -ac 1 speech.wav');
?>
