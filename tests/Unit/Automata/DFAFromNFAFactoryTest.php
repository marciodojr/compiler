<?php

namespace Mdojr\Compiler\Automata\Test\Unit;

use Mdojr\Compiler\Automata\Alphabet;
use Mdojr\Compiler\Automata\DFAFromNFAFactory;
use Mdojr\Compiler\Automata\NFA;
use PHPUnit\Framework\TestCase;

class DFAFromNFAFactoryTest extends TestCase
{
    public function testCreateDFAFromNFAAndAcceptInputSuccess()
    {
        $nfa = $this->createNFAForTest();

        $input = 'aaaaaaaaaaba';
        $nfaAcceptanceResult = $nfa->acceptInput($input);

        $this->assertTrue($nfaAcceptanceResult);

        $dfa = DFAFromNFAFactory::createDFAFromNFA($nfa);

        $nfaAcceptanceResult = $dfa->acceptInput($input);

        $this->assertTrue($nfaAcceptanceResult);

        // 00. δ([n0], ϵ) = [n0]
        // 01. δ([n0], 'a') = [n0, n1]
        // 02. δ([n0, n3, n1], 'a') = [n0, n1]
        // 03. δ([n0, n3, n1], 'a') = [n0, n1]
        // 04. δ([n0, n3, n1], 'a') = [n0, n1]
        // 05. δ([n0, n3, n1], 'a') = [n0, n1]
        // 06. δ([n0, n3, n1], 'a') = [n0, n1]
        // 07. δ([n0, n3, n1], 'a') = [n0, n1]
        // 08. δ([n0, n3, n1], 'a') = [n0, n1]
        // 09. δ([n0, n3, n1], 'a') = [n0, n1]
        // 10. δ([n0, n3, n1], 'a') = [n0, n1]
        // 11. δ([n0, n3, n1], 'b') = [n2]
        // 12. δ([n3, n2], 'a') = [n3]
        // $nfaTransitions = $nfa->getLastInputTransitions();

        // 00. δ(n0, 'a') = n0_n3_n1
        // 01. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 02. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 03. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 04. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 05. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 06. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 07. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 08. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 09. δ(n0_n3_n1, 'a') = n0_n3_n1
        // 10. δ(n0_n3_n1, 'b') = n3_n2
        // 11. δ(n3_n2, 'a') = n3
        // $dfaTransitions = $dfa->getLastInputTransitions();

        // print_r($nfaTransitions);
        // print_r($dfaTransitions);
    }

    public function testCreateDFAFromNFAAndAcceptInputError()
    {
        $nfa = $this->createNFAForTest();

        $input = '';
        $nfaAcceptanceResult = $nfa->acceptInput($input);

        $this->assertFalse($nfaAcceptanceResult);

        $dfa = DFAFromNFAFactory::createDFAFromNFA($nfa);

        $nfaAcceptanceResult = $dfa->acceptInput($input);

        $this->assertFalse($nfaAcceptanceResult);
    }

    /**
     * NFA que reconhece aa*(ϵ|b|ba)
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
