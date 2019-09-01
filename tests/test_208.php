<?php
$w = "и";
preg_match("/^[А-Яа-яЁё]+[\-]*[а-я]*ё*/u", $w, $res);
print_r($res);

