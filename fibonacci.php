<?php
class Fibonacci implements Iterator {
    // Cached sequence.
    private static array $bigFibonacciSequence;
    private array $fibonacciSequence;
    private int $index = 0;
    private ?int $sequenceLength;
    private bool $calculated = false;
    // No parameter given = infinite mode
    public function __construct(?int $sequenceLength = null) {
        $this->sequenceLength = $sequenceLength;
        // If it's not infinite mode, check if cache already has a sequence calculated. If it's larger, no need to calculate. If it's smaller, start from there.
        // If no cache, initialize a new sequence.
        if ($sequenceLength !== null && isset(self::$bigFibonacciSequence)) {
            if ($sequenceLength <= count(self::$bigFibonacciSequence)) {
                $this->fibonacciSequence = array_slice(self::$bigFibonacciSequence,0,$sequenceLength);
                $this->calculated = true;
            } else {
                $this->fibonacciSequence = self::$bigFibonacciSequence;
            }
        } else if ($sequenceLength === 0) {
            $this->fibonacciSequence = [];
        } else if ($sequenceLength == 1) {
            $this->fibonacciSequence = [0];
        } else {
            $this->fibonacciSequence = [0,1];
        }
    }
    public function current(): mixed {
        // Only calculate if it hasn't been done
        if ($this->calculated==false && $this->sequenceLength !== null) {
            $iterations = count($this->fibonacciSequence);
            while ($iterations < $this->sequenceLength) {
                $this->fibonacciSequence[] = $this->fibonacciSequence[$iterations-2]+$this->fibonacciSequence[$iterations-1];
                $iterations++;
            }
            $this->calculated = true;
            // If cached sequence is smaller than calculated sequence or if it doesn't exist yet, cache this sequence.
            if (!isset(self::$bigFibonacciSequence) || count(self::$bigFibonacciSequence) < count($this->fibonacciSequence)) {
                self::$bigFibonacciSequence = $this->fibonacciSequence;
            }
        }
        return $this->fibonacciSequence[$this->index];
    }
    public function key(): mixed {
        return $this->index;
    }
    public function next(): void {
        $this->index += 1;
        // Infinite mode can't be precalculated in current() method, so this does it.
        if ($this->sequenceLength === null && !isset($this->fibonacciSequence[$this->index])) {
            $length = count($this->fibonacciSequence);
            $this->fibonacciSequence[] = $this->fibonacciSequence[$length-2]+$this->fibonacciSequence[$length-1];
        }
    }
    public function rewind(): void {
        $this->index = 0;
    }
    public function valid(): bool {
        return $this->index < count($this->fibonacciSequence);
    }
    public static function first(?int $sequenceLength = null): void {
        $fibonacci = new Fibonacci($sequenceLength);
        foreach($fibonacci as $number) {
            echo $number." ";
        }
        echo "<br>";
    }
    public static function range(int $sequenceStart, ?int $sequenceEnd = null): void {
        $fibonacci = new Fibonacci($sequenceEnd);
        $i = 0;
        foreach($fibonacci as $number) {
            $i++;
            if ($i >= $sequenceStart) {
                echo $number." ";
            }
        }
        echo "<br>";
    }
}


Fibonacci::first(0);
Fibonacci::first(2);
?>