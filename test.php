<?php
include 'MagicSquare.php';

$magicSquare = new IntZone\MathZ\MagicSquare();
$n = 8;
$result = $magicSquare->generate($n);
echo $magicSquare->render($result);
