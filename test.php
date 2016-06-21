<?php
include 'MagicSquare.php';

$magicSquare = new IntZone\MathZ\MagicSquare();
$n = 8;

try {
    $result = $magicSquare->generate($n);
    echo $magicSquare->render($result);
    printf(
        '<br>Sum per row/col/diagonal for %1$d x %1$d magic square: %2$d',
        $n,
        $magicSquare->computeSum($n)
    );
} catch (Exception $e) {
    echo $e->getMessage();
}
