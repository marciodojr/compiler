<?php

namespace Mdojr\Compiler\Automata;

class DFA extends AbstractNFA
{
    protected function executeTransitionsForInput(array $inputElements): array
    {
        $currentState =  $this->startState;
        foreach ($inputElements as $inputElement) {
            $newState = $this->executeTransitionForStateAndInputElement($currentState, $inputElement)[0] ?? null;
            $this->storeTransition($currentState, $inputElement, $newState ?? '<invalid>');
            $currentState = $newState;

            if (is_null($currentState)) {
                break;
            }
        }

        return [$currentState];
    }
}
