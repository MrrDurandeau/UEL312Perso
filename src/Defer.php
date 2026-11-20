<?php declare(strict_types=1);

class Callback {
    public function __construct(
        private mixed $cb,
        private array $args = [],
    ) {}

    public function call(): void {
        call_user_func_array($this->cb, $this->args);
    }
}

class Defer {
    private array $callables = [];
    public function __construct() {}
    public static function init(&$var) {
        $var = new Defer();
    }
    public function __invoke($callable, ...$args) {
        array_unshift($this->callables, new Callback($callable, $args));
    }
    public function __destruct() {
        foreach ($this->callables as $callable) {
            $callable->call();
        }
    }
}
function world() {
    echo "World" . PHP_EOL;
}
function hello() {
    Defer::init($abc);
    $abc("world");

    echo "Hello";
}
hello();

?>
