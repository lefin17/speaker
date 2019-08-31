<?php
$txt = file_get_contents("../tmp/3.tmp");
$txt = iconv("koi8", 'utf8', $txt);
print $txt;