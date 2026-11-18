<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    private static array $fibonacciSequence = [0,1];
    private int $index = 0;
    // For rewind() method.
    private int $baseIndex = 0;
    private ?int $sequenceLength;
    // No sequenceLength specified = infinite mode.
    public function __construct(?int $sequenceLength = null, int $index = 0) {
        $this->sequenceLength = $sequenceLength;
        $this->index = $index;
        $this->baseIndex = $index;
    }

    public static function first(?int $n = null): self {
        return new FibonacciSequence($n);
    }

    public static function range(int $start, ?int $length = null): self {
        if ($length === null) {
            return new FibonacciSequence(null, $start);
        }
        return new FibonacciSequence($start+$length, $start);
    }

    public function current(): mixed {
        if (!isset(self::$fibonacciSequence[$this->index])) {
            $this->fibonacci($this->index);
        }
        return self::$fibonacciSequence[$this->index];
    }

    public function key(): mixed {
        return $this->index;
    }

    public function next(): void {
        $this->index += 1;
    }

    public function rewind(): void {
        $this->index = $this->baseIndex;
    }

    public function valid(): bool {
        // Infinite mode never ends.
        if ($this->sequenceLength === null) {
            return true;
        }
        return $this->index < $this->sequenceLength;
    }
    private function fibonacci(int $n): void {
        if (!isset(self::$fibonacciSequence[$n-2]) || !isset(self::$fibonacciSequence[$n-1])) {
            $this->fibonacci($n-1);
        }
        $number = self::$fibonacciSequence[$n-2] + self::$fibonacciSequence[$n-1];
        self::$fibonacciSequence[] = $number;
    }
}
?>
