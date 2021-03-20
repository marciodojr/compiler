## Compiler related stuff (DFA's Regex ...)

## Init

```sh
make up # same as docker-compose up
make php # in another terminal window (access the php container)
vendor/bin/phpunit --testsuite u
```

## How to use.

Well I don't know if it is useful. It's just some classes to work with automata.

What you can do:

- Create a DFA and check if it accepts an input.
- Create a NFA and check if it accepts an input.
- Create a DFA from a NFA using the subset construction algorithm via DFAFromNFAFactory.
- It is possible to get the transition's array after execution via `getLastInputTransitions()`.

Example:

```
This test printed output: Array
(
    [0] => δ(n0, 'a') = n0_n3_n1
    [1] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [2] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [3] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [4] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [5] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [6] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [7] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [8] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [9] => δ(n0_n3_n1, 'a') = n0_n3_n1
    [10] => δ(n0_n3_n1, 'b') = n3_n2
    [11] => δ(n3_n2, 'a') = n3
)
```

## Examples

1. Creating a DFA

```php
/**
* DFA for aa*(ϵ|b|ba)
*
*                  --a--
*                  |   |
*                  v   |
* --> (q0) --a--> ((q1)) --b--> ((q2)) --a--> ((q3))
*
*/
$dfaStates = [
    'q0', // 0
    'q1', // 1
    'q2', // 2
    'q3', // 3
];

$dfaAlphabet = ['a', 'b'];

$dfaTransistionFn = [
    $dfaStates[0] => [
        $dfaAlphabet[0] => [
            $dfaStates[1],
        ],
    ],
    $dfaStates[1] => [
        $dfaAlphabet[0] => [
            $dfaStates[1],
        ],
        $dfaAlphabet[1] => [
            $dfaStates[2],
        ],
    ],
    $dfaStates[2] => [
        $dfaAlphabet[0] => [
            $dfaStates[3],
        ],
    ],
    $dfaStates[3] => [],
];

$dfa = new DFA($dfaStates, $dfaAlphabet, $dfaTransistionFn, $dfaStates[0], [
    $dfaStates[1],
    $dfaStates[2],
    $dfaStates[3],
]);

// $accept is true
$accept = $dfa->acceptInput('a');
```

2. Creating a DFA from a NFA

please take a look at the file [DFAFromNFAFactoryTest](tests/Unit/Automata/DFAFromNFAFactoryTest.php).