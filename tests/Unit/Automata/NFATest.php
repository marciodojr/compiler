<?php

namespace Mdojr\Compiler\Automata\Test\Unit;

use Mdojr\Compiler\Automata\Alphabet;
use Mdojr\Compiler\Automata\NFA;
use PHPUnit\Framework\TestCase;

class NFATest extends TestCase
{
    public function testNFAAcceptsInput0Success(): void
    {
        $input0 = 'a';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input0);

        $this->assertTrue($accept);

        // print_r($nfa->getLastInputTransitions());
    }

    public function testNFAAcceptsInput1Success(): void
    {
        $input1 = 'aaba';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input1);

        $this->assertTrue($accept);

        // print_r($nfa->getLastInputTransitions());
    }

    public function testNFAAcceptsInput2Success(): void
    {
        $input2 = 'aa';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input2);

        $this->assertTrue($accept);
    }

    public function testNFAAcceptsInput3Success(): void
    {
        $input3 = 'aab';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input3);

        $this->assertTrue($accept);
    }

    public function testNFAAcceptsInput4Success(): void
    {
        $input4 = 'aaaba';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input4);

        $this->assertTrue($accept);
    }

    public function testNFAAcceptsInput5Error(): void
    {
        $input6 = 'aabaa';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input6);

        $this->assertFalse($accept);
    }

    public function testNFAAcceptsInput6Error(): void
    {
        $input6 = '';
        $nfa = $this->createNFAForTest();

        $accept = $nfa->acceptInput($input6);

        $this->assertFalse($accept);
    }

    /**
     * NFA for aa*(ϵ|b|ba)
     *
     *     --a--                    ------ϵ-------
     *     |   |                    |            |
     *     v   |                    |            v
     * --> (n0) --a--> (n1) --b--> (n2) --a--> ((n3))
     *                  |                         ^
     *                  |                         |
     *                  ---------------ϵ-----------
     *
     * @return NFA
     */
    private function createNFAForTest(): NFA
    {
        $nfaStates = [
            'n0', // 0
            'n1', // 1
            'n2', // 2
            'n3', // 3
        ];
        $nfaAlphabet = ['a', 'b'];

        $nfaTransistionFn = [
            $nfaStates[0] => [
                $nfaAlphabet[0] => [
                    $nfaStates[0],
                    $nfaStates[1],
                ],
            ],
            $nfaStates[1] => [
                $nfaAlphabet[1] => [
                    $nfaStates[2],
                ],
                Alphabet::EPSILON => [
                    $nfaStates[3],
                ]
            ],
            $nfaStates[2] => [
                $nfaAlphabet[0] => [
                    $nfaStates[3],
                ],
                Alphabet::EPSILON => [
                    $nfaStates[3],
                ]
            ],
            $nfaStates[3] => [],
        ];

        return new NFA($nfaStates, $nfaAlphabet, $nfaTransistionFn, $nfaStates[0], [$nfaStates[3]]);
    }
}
