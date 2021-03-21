<?php

namespace Mdojr\Compiler\Automata;

use InvalidArgumentException;

abstract class AbstractNFA
{
    protected array $states;
    protected array $alphabet;
    protected array $transistionFn;
    protected string $startState;
    protected array $acceptanceStates;
    protected array $transitions;

    public function __construct(
        array $states,
        array $alphabet,
        array $transistionFn,
        string $startState,
        array $acceptanceStates
    ) {
        $this->states = $this->parseStates($states);
        $this->alphabet = $alphabet;
        $this->transistionFn = $transistionFn; // some day this will be validated (or not)

        if (!$this->isInStateArray([$startState])) {
            throw new InvalidArgumentException("Start state {$this->startState} not in the state set.");
        }

        if (!$this->isInStateArray($acceptanceStates)) {
            throw new InvalidArgumentException("Acceptance states not in the state set.");
        }

        $this->startState = $startState;
        $this->acceptanceStates = $acceptanceStates;
    }

    public function acceptInput(string $input): bool
    {
        $this->transitions = [];
        $inputElements = str_split($input);
        $finalStates = $this->executeTransitionsForInput($inputElements);

        return $this->hasAtLeastOneAcceptanceState($finalStates);
    }

    public function getLastInputTransitions()
    {
        return $this->transitions;
    }

    abstract protected function executeTransitionsForInput(array $inputElements): array;

    protected function storeTransition(string $fromStateOrStateArray, string $inputElement, string $toStateOrStateArray)
    {
        $inputElement = Alphabet::EPSILON != $inputElement
            ? sprintf("'%s'", $inputElement)
            : $inputElement;

        $this->transitions[] = sprintf("δ(%s, %s) = %s", $fromStateOrStateArray, $inputElement, $toStateOrStateArray);
    }

    protected function executeTransitionForStateAndInputElement(string $state, string $inputElement): array
    {
        $newStates = $this->getNewStates($state, $inputElement);

        if (Alphabet::EPSILON === $inputElement) {
            array_unshift($newStates, $state);
        }

        return array_unique($newStates);
    }

    public function calculateEpsilonClosure()
    {
        $epsilonClosure = [];
        $workList = [];
        foreach ($this->states as $state) {
            $epsilonClosure[$state] = [$state];
            $workList[$state] = $state;
        }

        do {
            $currentState = array_shift($workList);
            $directEpsilonClosureStates = $this->executeTransitionForStateAndInputElement(
                $currentState,
                Alphabet::EPSILON
            );

            if (count($directEpsilonClosureStates) > count($epsilonClosure[$currentState])) {
                $epsilonClosure[$currentState] = $directEpsilonClosureStates;
                foreach ($directEpsilonClosureStates as $state) {
                    $workList[$state] = $state;
                }
            }
        } while ($workList);

        return $epsilonClosure;
    }

    private function getNewStates(string $state, string $inputElement)
    {
        return $this->transistionFn[$state][$inputElement] ?? [];
    }


    private function parseStates(array $states)
    {
        $parsedStates = [];
        foreach ($states as $state) {
            if (is_string($state) && !in_array($state, $parsedStates)) {
                $parsedStates[] = $state;
            }
        }

        return $parsedStates;
    }

    private function isInStateArray(array $statesToCheck)
    {
        return array_intersect($statesToCheck, $this->states) == $statesToCheck;
    }

    public function hasAtLeastOneAcceptanceState(array $states): bool
    {
        return (bool) array_intersect($states, $this->acceptanceStates);
    }

    public function getAlphabet()
    {
        return $this->alphabet;
    }

    public function getStartState()
    {
        return $this->startState;
    }

    public function getStates()
    {
        return $this->states;
    }
}
