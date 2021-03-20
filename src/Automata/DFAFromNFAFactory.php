<?php

namespace Mdojr\Compiler\Automata;

class DFAFromNFAFactory
{
    public static function createDFAFromNFA(NFA $nfa)
    {
        $nfa = clone $nfa;

        $epsilonClosure = self::getDFAStartState($nfa);
        $transitionAndStates = self::getDFATransitionFunctionAndStates($epsilonClosure, $nfa);

        $dfaStates = $transitionAndStates['states'];
        $alphabet = $nfa->getAlphabet();
        $dfaTransitionFn = $transitionAndStates['transition_function'];
        $dfaStartState = $dfaStates[0];
        $dfaAcceptanceStates = $transitionAndStates['acceptance_states'];

        return new DFA($dfaStates, $alphabet, $dfaTransitionFn, $dfaStartState, $dfaAcceptanceStates);
    }

    private static function getDFAStartState(NFA $nfa)
    {
        $nfaStartState = $nfa->getStartState();
        $epsilonClosure = $nfa->executeTransitionForStatesAndInputElement([$nfaStartState], Alphabet::EPSILON);

        return $epsilonClosure;
    }

    private static function getDFATransitionFunctionAndStates(array $epsilonClosure, NFA $nfa)
    {
        $nfaAlphabet = $nfa->getAlphabet();
        $workerList = [$epsilonClosure];
        $dfaTransitionFn = [];

        $dfaState = self::generateDFAStateRepresentation($epsilonClosure);
        $dfaStates = [
            $dfaState,
        ];
        $dfaAcceptanceStates = [];
        do {
            $currentStates = array_shift($workerList);

            $dfaState = self::generateDFAStateRepresentation($currentStates);
            if ($nfa->hasAtLeastOneAcceptanceState($currentStates)) {
                $dfaAcceptanceStates[] = $dfaState;
            }

            foreach ($nfaAlphabet as $element) {
                $newStates = $nfa->executeTransitionForStatesAndInputElement($currentStates, $element);
                $newStates = $nfa->executeTransitionForStatesAndInputElement($newStates, Alphabet::EPSILON);

                $newDfaState = self::generateDFAStateRepresentation($newStates);

                $dfaTransitionFn[$dfaState][$element] = $newStates ? [$newDfaState] : [];

                if (!in_array($newDfaState, $dfaStates)) {
                    $dfaStates[] = $newDfaState;
                    $workerList[] = $newStates;
                }
            }
        } while ($workerList);

        return [
            'transition_function' => $dfaTransitionFn,
            'states' => $dfaStates,
            'acceptance_states' => $dfaAcceptanceStates,
        ];
    }

    private static function generateDFAStateRepresentation(array $states)
    {
        return implode('_', $states);
    }
}
