<?php declare(strict_types=1);

class BitArrayIterator implements Iterator {
    private string $bits;
    private int $index = 0;
    private int $length;
    public function __construct(string $bits, int $slice = 1) {
        if ($slice < 1) {
            throw new InvalidArgumentException("Slice length can't be inferior to 1");
        }
        $this->bits = $bits;
        $this->length = $slice;
    }
    public function current(): mixed {
        if ($this->length === 1) {
            // L'exemple donné sur les consignes indique "int: 0b0", ce que je n'ai pas compris. J'ai utilisé des strings à la place.
            return "0b".$this->bits[$this->index];
        } else {
            return new BitArray(substr($this->bits, $this->index, $this->length));
        }
    }
    public function key(): int {
        return $this->index;
    }
    public function next(): void {
        $this->index += $this->length;
    }
    public function rewind(): void {
        $this->index = 0;
    }
    public function valid(): bool {
        return $this->index < strlen($this->bits);
    }
}

class BitArray implements ArrayAccess, Countable, IteratorAggregate, Stringable {
    // Je n'ai pas compris le but des variables qui étaient fournies avec le boilerplate.
    // J'ai décidé de stocker mes bits en string, ce qui m'a l'air plus efficace qu'un array, et m'était plus compréhensible qu'avec des int.
    private string $bits = "";

    public function __construct(string $bits = "") {
        $this->bits = $bits;
    }

    public static function fromInt(int $from) {
        return new BitArray(strrev(decbin($from)));
    }

    public static function fromString(string $from) {
        $string = $from;
        if ($string[0].$string[1] === "0b") {
            $string = str_replace("0b","", $string);
        }
        if (preg_match('/^[01]+$/', $string)) {
            return new BitArray(strrev($string));
        }
    }

    public function slice(int $start = 0, int $length = -1): self {
        if ($start < 0) {
            throw new InvalidArgumentException('$start parameter can\'t be inferior to 0');
        }
        $length = ($length < 0) ? null : $length;
        return new self(substr($this->bits, $start, $length));
    }

    public function set(array $bits, int $start = 0): void {
        $end = count($bits);
        $i = 0;
        while ($i < $end) {
            $this->offsetSet($i+$start, $this->validateBit($bits[$i]));
            $i++;
        }
    }

    public function unset(int $start, int $length = -1): void {
    $i = $start;    
    if ($length > 0) {
            $end = $start + $length;
            while ($i < $end) {
                $this->offsetUnset($i);
                $i++;
            }
        } else if ($length < 0) {
            $end = strlen($this->bits);
            while ($i < $end) {
                $this->offsetUnset($i);
                $i++;
            }
        }
    }

    public function offsetExists(mixed $offset): bool {
        return $offset < strlen($this->bits);
    }

    public function offsetGet(mixed $offset): int {
        return intval($this->bits[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $this->bits[$offset] = $value;
        $this->bits = str_replace(" ","0", $this->bits);
    }

    public function offsetUnset(mixed $offset): void {
        $this->bits[$offset] = '0';
        $this->bits = str_replace(" ","0", $this->bits);
    }

    public function count(): int {
        return strlen(rtrim($this->bits, '0'));
    }

    public function getIterator(): BitArrayIterator {
        return new BitArrayIterator($this->bits);
    }

    public function getSlicesIterator(int $length): BitArrayIterator {
        return new BitArrayIterator($this->bits, $length);
    }

    public function __toString(): string {
        return "0b".strrev($this->bits);
    }

    private function validateBit(mixed $bit): int {
        $valid = ['0','1',0,1,true,false];
        if (in_array($bit, $valid)) {
            return (int) $bit;
        } else {
            throw new InvalidArgumentException('Invalid bit passed as parameter');
        }
    }
}

$bits = BitArray::fromString("0b1010010111110000");
$slices = $bits->getSlicesIterator(4);
foreach ($slices as $slice) {
 echo gettype($slice) . ": " . $slice . '<br>';
}
// output: object: 0b0000
// output: object: 0b1111
// output: object: 0b0101
// output: object: 0b1010
foreach ($bits as $bit) {
 echo gettype($bit) . ": " . $bit . '<br>';
}
// output: int: 0b0
// output: int: 0b0
// output: int: 0b0
// output: int: 0b0
// output: int: 0b1
// ...
// output: int: 0b1
?>
