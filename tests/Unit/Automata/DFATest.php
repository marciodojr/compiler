<?php

namespace Mdojr\Compiler\Automata\Test\Unit;

use Mdojr\Compiler\Automata\DFA;
use PHPUnit\Framework\TestCase;

class DFATest extends TestCase
{
    public function testDFAAcceptsInput0Success(): void
    {
        $input0 = 'a';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input0);

        $this->assertTrue($accept);

        // print_r($dfa->getLastInputTransitions());
    }

    public function testDFAAcceptsInput1Success(): void
    {
        $input1 = 'aaba';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input1);

        $this->assertTrue($accept);

        // print_r($dfa->getLastInputTransitions());
    }

    public function testDFAAcceptsInput2Success(): void
    {
        $input2 = 'aa';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input2);

        $this->assertTrue($accept);
    }

    public function testDFAAcceptsInput3Success(): void
    {
        $input3 = 'aab';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input3);

        $this->assertTrue($accept);
    }

    public function testDFAAcceptsInput4Success(): void
    {
        $input4 = 'aaaba';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input4);

        $this->assertTrue($accept);
    }

    public function testDFAAcceptsInput5Error(): void
    {
        $input6 = 'aabaa';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input6);

        // print_r($dfa->getLastInputTransitions());
        $this->assertFalse($accept);
    }

    public function testDFAAcceptsInput6Error(): void
    {
        $input6 = '';
        $dfa = $this->createDFAForTest();

        $accept = $dfa->acceptInput($input6);

        $this->assertFalse($accept);
    }

    /**
     * DFA for aa*(ϵ|b|ba)
     *
     *                  --a--
     *                  |   |
     *                  v   |
     * --> (q0) --a--> ((q1)) --b--> ((q2)) --a--> ((q3))
     *
     * @return NFA
     */
    private function createDFAForTest(): DFA
    {
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
                    // Remember, even if it is implemented as an array, for a DFA only one state is allowed
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

        return new DFA($dfaStates, $dfaAlphabet, $dfaTransistionFn, $dfaStates[0], [
            $dfaStates[1],
            $dfaStates[2],
            $dfaStates[3],
        ]);
    }
}
