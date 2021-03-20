<?php

namespace Mdojr\Compiler\Automata;

class NFA extends AbstractNFA
{
    protected function executeTransitionsForInput(array $inputElements): array
    {
        // ϵ-closure
        $currentStateArray =  $this->executeTransitionForStatesAndInputElement([$this->startState], Alphabet::EPSILON);
        foreach ($inputElements as $inputElement) {
            $currentStateArray = $this->executeTransitionForStatesAndInputElement(
                $currentStateArray,
                $inputElement
            );

            if (!$currentStateArray) {
                break;
            }

            // ϵ-closure
            $currentStateArray = $this->executeTransitionForStatesAndInputElement(
                $currentStateArray,
                Alphabet::EPSILON,
                false
            );
        }

        return $currentStateArray;
    }

    public function executeTransitionForStatesAndInputElement(
        array $states,
        string $inputElement,
        bool $storeTransition = true
    ) {
        $newStates = [];
        foreach ($states as $currentState) {
            $statesFromTransition = $this->executeTransitionForStateAndInputElement($currentState, $inputElement);
            $newStates = array_merge($newStates, $statesFromTransition);
        }

        $newStates = array_unique($newStates);

        if ($storeTransition) {
            $fromStateArray = implode(', ', $states);
            $toStateArray = implode(', ', $newStates);
            $this->storeTransition("[$fromStateArray]", $inputElement, "[$toStateArray]");
        }

        $isEpsilonTransition = Alphabet::EPSILON == $inputElement;

        // ϵ-closure condition: keep following ϵ-transitions
        if ($isEpsilonTransition && count($newStates) != count($states)) {
            return $this->executeTransitionForStatesAndInputElement(
                $newStates,
                $inputElement,
                $storeTransition
            );
        }

        return $newStates;
    }
}
