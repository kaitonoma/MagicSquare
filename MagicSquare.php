<?php
/**
 * @link   https://github.com/zionsg/MagicSquare for repository
 * @author Zion Ng <zion@intzone.com>
 * @since  2016-06-19T20:00+08:00
 */

namespace IntZone\MathZ;

use DomainException;
use InvalidArgumentException;

/**
 * Magic square constructor
 *
 * @todo Only order 4m supported for now
 */
class MagicSquare
{
    /**
     * Supported orders of magic squares
     *
     * @var array
     */
    protected $orders = [];

    /**
     * Constructor
     *
     * Setup callbacks for supported orders of magic squares.
     */
    public function __construct()
    {
        $this->orders = [
            /*
            'odd' => [
                'test' => function ($n) { return (1 === $n % 2); },
                'computeWidth' => function ($cellCount) {
                    $sqrt = (int) ceil(sqrt($cellCount));
                    return $sqrt + (0 === $sqrt % 2 ? 1 : 0);
                },
                'generator' => 'generateOdd',
            ],
            */

            '4m' => [
                'test' => function ($n) { return (0 === $n % 4); },
                'computeWidth' => function ($cellCount) { return (int) (ceil(sqrt($cellCount) / 4) * 4); },
                'generator' => 'generate4m',
            ],
        ];
    }

    /**
     * Compute minimum width of magic square needed to contain a number of cells
     *
     * A cell may contain a character, number, word, etc. A 4x4 magic square consists of 16 cells.
     * This goes thru all the supported orders, compute the respective widths and finds the smallest.
     *
     * @param  int $cellCount
     * @throws InvalidArgumentException if $cellCount is not a positive integer
     * @return int Returns 0 if width cannot be computed
     */
    public function computeWidth($cellCount)
    {
        $this->assertPositiveInteger($cellCount);

        $widths = [];
        foreach ($this->orders as $order) {
            $widthFn = $order['computeWidth'];
            if (is_callable($widthFn)) {
                $widths[] = $widthFn($cellCount);
            }
        }

        return ($widths ? min($widths) : 0);
    }

    /**
     * Create n x n magic square with numbers 1 to n
     *
     * @param  int $n
     * @throws InvalidArgumentException if n is not a positive integer
     * @throws DomainException if unable to generate for n
     * @return array [[<row 1 column 1>, <row 1 column 2>], [<row 2 column 1>, <row 2 column 2]]
     */
    public function generate($n)
    {
        $this->assertPositiveInteger($n);

        foreach ($this->orders as $order) {
            $testFn = $order['test'];
            if (is_callable($testFn) && $testFn($n)) {
                $generator = $order['generator'];
                return $this->$generator($n);
            }
        }

        throw new DomainException("Unable to generate {$n} x {$n} magic square");
    }

    /**
     * Render magic square as HTML table
     *
     * @param  array  $magicSquare     Result of generate()
     * @param  bool   $useDefaultStyle Whether to use default CSS styling for table
     * @param  string $tableClass      Optional CSS class for HTML table
     * @return string
     */
    public function render(array $magicSquare, $useDefaultStyle = true, $tableClass = '')
    {
        $n = count($magicSquare);

        $tableStyle = $useDefaultStyle ? 'border-collapse:collapse; border-spacing:0;' : '';
        $tdStyle = $useDefaultStyle
                 ? 'border:1px solid black; font-family:monospace; text-align:center; vertical-align:middle;'
                 : '';

        $output = sprintf('<table class="%s" style="%s">', $tableClass, $tableStyle);
        for ($row = 0; $row < $n; $row++) {
            $output .= '<tr>';
            for ($col = 0; $col < $n; $col++) {
                $output .= sprintf(
                    '<td style="%s">%s</td>',
                    $tdStyle,
                    $magicSquare[$row][$col]
                );
            }
            $output .= '</tr>';
        }
        $output .= '</table>';

        return $output;
    }

    /**
     * Assert that a variable is a positive integer
     *
     * @param  mixed $var
     * @throws InvalidArgumentException if assertion fails
     * @return bool
     */
    protected function assertPositiveInteger($var)
    {
        $result = (is_int($var) && $var > 0);

        if (!$result) {
            throw new InvalidArgumentException("{$var} is not a positive integer");
        }

        return $result;
    }

    /**
     * Create n x n magic square where n = 4m, m being a positive integer
     *
     * @param  int $n
     * @throws InvalidArgumentException if n is not a positive integer
     * @throws InvalidArgumentException if n is not a multiple of 4
     * @return array [[<row 1 column 1>, <row 1 column 2>], [<row 2 column 1>, <row 2 column 2]]
     */
    protected function generate4m($n)
    {
        $this->assertPositiveInteger($n);

        if ($n % 4 !== 0) {
            throw new InvalidArgumentException("{$n} is not a multiple of 4");
        }

        $baseGrid = [
            [0, 1, 1, 0],
            [1, 0, 0, 1],
            [1, 0, 0, 1],
            [0, 1, 1, 0],
        ];

        $grid = [];
        for ($row = 0; $row < $n; $row++) {
            for ($col = 0; $col < $n; $col++) {
                $grid[$row][$col] = $baseGrid[$row % 4][$col % 4];
            }
        }

        $result = [];

        // Step 1: Counting from 1 to n, go from FIRST cell (topmost left), left to right, top to bottom
        //         and fill up the cells where grid indicates '1'.
        $i = 0;
        for ($row = 0; $row < $n; $row++) {
            for ($col = 0; $col < $n; $col++) {
                $i++; // increment as each cell is passed, even if not used
                if (0 === $grid[$row][$col]) {
                    continue;
                }
                $result[$row][$col] = $i;
            }
        }

        // Step 2: Counting from 1 to n, go from LAST cell (bottommost right), right to left, bottom to top
        //         and fill up the cells where grid indicates '0'.
        $i = 0;
        for ($row = ($n - 1); $row >= 0; $row--) {
            for ($col = ($n - 1); $col >= 0; $col--) {
                $i++; // increment as each cell is passed, even if not used
                if (1 === $grid[$row][$col]) {
                    continue;
                }
                $result[$row][$col] = $i;
            }
        }

        return $result;
    }
}
