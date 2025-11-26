<?php

$projectRoot = dirname(__DIR__);
$fontDir = $projectRoot.'/storage/app/resume-templates/fonts';

chdir($fontDir);
$argc = 2;
$argv = ['makefont.php', 'dummy'];
require $projectRoot.'/vendor/setasign/fpdf/makefont/makefont.php';

MakeFont('Lora-Regular.ttf');
MakeFont('Lora-Bold.ttf');
