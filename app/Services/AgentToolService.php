<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Transaction;

class AgentToolService
{
    protected array $orchestrator = [];

    protected ?string $userId = null;

    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected FinanceAnalyzeService $financeAnalyze)
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
    public function getOrchestrator(): array
    {
        return $this->orchestrator;
    }

    public function setOrchestrator(array $items): void
    {
        $this->orchestrator = $items;
    }

    public function addOrchestratorItem(array $item): void
    {
        $this->orchestrator[] = $item;
    }

    public function updateOrchestratorItem(int $index, array $item): void
    {
        if (array_key_exists($index, $this->orchestrator)) {
            $this->orchestrator[$index] = $item;
        }
    }

    public function call(array $orchestrator, ?string $userId = null): AgentToolCallResult
    {
        if ($userId !== null) {
            $this->userId = (string) $userId;
        } else {
            $this->resolveUserIdFromRequest();
        }
        $this->setOrchestrator($orchestrator);
        $results = [];

        $index = 0;
        while ($index < count($this->orchestrator)) {
            $item = $this->orchestrator[$index];
            $functionName = $item['function'] ?? null;
            $params = $item['param'] ?? ($item['arguments'] ?? ($item['params'] ?? []));

            if (! is_string($functionName) || $functionName === '') {
                $results[] = ['index' => $index, 'error' => 'missing_function'];
                $index++;

                continue;
            }

            if (! method_exists($this, $functionName)) {
                $results[] = ['index' => $index, 'function' => $functionName, 'error' => 'method_not_found'];
                $index++;

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
            $index++;
        }

        return new AgentToolCallResult($results);
    }

    private function resolveUserIdFromRequest(): void
    {
        if ($this->userId !== null) {
            return;
        }
        try {
            /** @var UserService $users */
            $users = app(UserService::class);
            $token = request()->cookie('user_token');
            $user = $users->getByToken($token);
            if ($user) {
                $this->userId = (string) $user->id;
            }
        } catch (\Throwable $e) {
        }
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
        try {
            $this->resolveUserIdFromRequest();
            if (! $this->userId) {
                logger()->warning('transaction_in_unauthorized');

                return null;
            }

            $tx = Transaction::createIn($this->userId, $amount, $note, $date);
            logger()->info('transaction_in', [
                'user_id' => $this->userId,
                'amount' => $amount,
                'note' => $note,
                'date' => $date,
            ]);

            return $tx;
        } catch (\Throwable $e) {
            logger()->error('transaction_in_error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    protected function transaction_out(int $amount, string $note, string $date)
    {
        try {
            $this->resolveUserIdFromRequest();
            if (! $this->userId) {
                logger()->warning('transaction_out_unauthorized');

                return null;
            }

            $tx = Transaction::createOut($this->userId, $amount, $note, $date);
            logger()->info('transaction_out', [
                'user_id' => $this->userId,
                'amount' => $amount,
                'note' => $note,
                'date' => $date,
            ]);

            return $tx;
        } catch (\Throwable $e) {
            logger()->error('transaction_out_error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    protected function persona_chat(string $reason, ?array $premessages): string
    {
        $messages = $premessages;
        if ($messages === null || (is_array($messages) && in_array('run', $messages, true))) {
            try {
                $this->resolveUserIdFromRequest();
                if ($this->userId) {
                    $messages = Message::lastTenRoleContentByUser($this->userId);
                }
            } catch (\Throwable $e) {
            }
        }

        return $this->agentChat->agentPersonaChat($reason, $messages);
    }

    protected function finance_analyze_chat(string $context)
    {
        $this->resolveUserIdFromRequest();
        if (! $this->userId) {
            logger()->warning('finance_analyze_chat_unauthorized');

            return;
        }
        $this->userId=2;
        $financeAnalysisResult = $this->agentChat->agentFinanceAnalyze($context);
        $financeAnalysis = $this->financeAnalyze->executeWithUser($financeAnalysisResult, $this->userId);

        $items = $this->getOrchestrator();

        $result = array_map(function ($n) use ($financeAnalysis) {
            if (($n['function'] ?? null) === 'persona_chat') {
                $n['param'] = $n['param'] ?? [];
                $n['param']['premessages'] = $financeAnalysis->generateMessages();
            }

            return $n;
        }, $items);

        $this->setOrchestrator($result);
        logger()->info('finance_analyze_chat', [
            'context' => $context,
            'financeAnalyzeResult' => $financeAnalysisResult,
        ]);
    }
}

class AgentToolCallResult
{
    protected array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function personaChat(): mixed
    {
        for ($i = count($this->items) - 1; $i >= 0; $i--) {
            $it = $this->items[$i];
            if (($it['function'] ?? null) === 'persona_chat') {
                return $it['result'] ?? null;
            }
        }

        return null;
    }
}
