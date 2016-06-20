<?php
include 'MagicSquare.php';

$magicSquare = new IntZone\MathZ\MagicSquare();
$n = 8;

try {
    $result = $magicSquare->generate($n);
    echo $magicSquare->render($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
