<?php

namespace App\Services;

class AgentToolService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat)
    {
        //
    }

    /**
     * Orchestrator items expected shape:
     * [
     *   ['function' => 'transaction_in', 'param' => ['amount' => 150000, 'note' => '...', 'date' => '2025-12-03']],
     *   ...
     * ]
     */
    public function call(array $orchestrator): array
    {
        $results = [];

        foreach ($orchestrator as $index => $item) {
            $functionName = $item['function'] ?? null;
            $params = $item['param'] ?? ($item['arguments'] ?? ($item['params'] ?? []));

            if (! is_string($functionName) || $functionName === '') {
                $results[] = ['index' => $index, 'error' => 'missing_function'];

                continue;
            }

            if (! method_exists($this, $functionName)) {
                $results[] = ['index' => $index, 'function' => $functionName, 'error' => 'method_not_found'];

                continue;
            }

            if (! is_array($params)) {
                $params = [$params];
            }

            try {
                $rm = new \ReflectionMethod($this, $functionName);
                $isIndexed = array_values($params) === $params;
                $args = $isIndexed ? $this->coerceArgsIndexed($params, $rm) : $this->coerceArgsAssoc($params, $rm);
                $return = $rm->invokeArgs($this, $args);
                $results[] = ['index' => $index, 'function' => $functionName, 'args' => $args, 'result' => $return];
            } catch (\Throwable $e) {
                $results[] = ['index' => $index, 'function' => $functionName, 'error' => 'exception', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }

    private function coerceArgsIndexed(array $params, \ReflectionMethod $rm): array
    {
        $args = [];
        $targets = $rm->getParameters();
        foreach ($targets as $i => $p) {
            $val = array_key_exists($i, $params) ? $params[$i] : ($p->isDefaultValueAvailable() ? $p->getDefaultValue() : null);
            $args[] = $this->coerceType($val, $p);
        }

        return $args;
    }

    private function coerceArgsAssoc(array $params, \ReflectionMethod $rm): array
    {
        $args = [];
        foreach ($rm->getParameters() as $p) {
            $name = $p->getName();
            $val = array_key_exists($name, $params) ? $params[$name] : ($p->isDefaultValueAvailable() ? $p->getDefaultValue() : null);
            $args[] = $this->coerceType($val, $p);
        }

        return $args;
    }

    private function coerceType(mixed $val, \ReflectionParameter $p): mixed
    {
        $type = $p->getType();
        if (! $type || ! $type->isBuiltin()) {
            return $val;
        }
        $t = $type->getName();
        if ($t === 'int') {
            return is_numeric($val) ? (int) $val : (is_int($val) ? $val : 0);
        }
        if ($t === 'float') {
            return is_numeric($val) ? (float) $val : (is_float($val) ? $val : 0.0);
        }
        if ($t === 'bool') {
            if (is_bool($val)) {
                return $val;
            }
            if (is_string($val)) {
                return in_array(strtolower($val), ['1', 'true', 'yes', 'on'], true);
            }
            if (is_numeric($val)) {
                return ((int) $val) !== 0;
            }

            return false;
        }
        if ($t === 'string') {
            return is_array($val) ? json_encode($val) : (string) $val;
        }

        return $val;
    }

    protected function transaction_in(int $amount, string $note, string $date)
    {
        logger()->info('transaction_in', [
            'amount' => $amount,
            'note' => $note,
            'date' => $date,
        ]);
    }

    protected function transaction_out(int $amount, string $note, string $date)
    {
        logger()->info('transaction_out', [
            'amount' => $amount,
            'note' => $note,
            'date' => $date,
        ]);
    }

    protected function persona_chat(string $reason)
    {
        logger()->info('persona_chat', [
            'message' => $reason,
        ]);
    }

    protected function finance_analyze_chat(string $context)
    {
        logger()->info('finance_analyze_chat', [
            'context' => $context,
        ]);
    }
}
