<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FinanceAnalyzeService
{
    public static function injectUserIdFilter(string $sql, string $userId): string
    {
        if (! preg_match('/^\s*SELECT/i', $sql)) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed.');
        }

        if (stripos($sql, 'WHERE') !== false) {
            $sql = preg_replace('/\bWHERE\b/i', "WHERE user_id = '".addslashes($userId)."' AND", $sql, 1);
        } else {
            $sql .= " WHERE user_id = '".addslashes($userId)."'";
        }

        return $sql;
    }

    public static function toCsv(?array $rows): string
    {
        if ($rows === null) {
            return 'gagal melakukan query';
        }
        if (empty($rows)) {
            return '';
        }
        $arrRows = array_map(fn ($r) => (array) $r, $rows);
        $allNull = true;
        foreach ($arrRows as $row) {
            foreach ($row as $v) {
                if ($v !== null) {
                    $allNull = false;
                    break 2;
                }
            }
        }
        if ($allNull) {
            return 'gagal melakukan query';
        }
        $headers = array_keys($arrRows[0]);
        $lines = [];
        $lines[] = implode(',', array_map(fn ($h) => '"'.str_replace('"', '""', (string) $h).'"', $headers));
        foreach ($arrRows as $row) {
            $lines[] = implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) ($v ?? '')).'"', $row));
        }

        return implode("\n", $lines);
    }

    public static function executeWithUser(FinanceAnalyzeResult $parsed, string|int $userId): FinanceAnalyzeResult
    {
        $items = $parsed->all();
        foreach ($items as $i => $item) {
            $sql = (string) ($item['sql'] ?? '');
            if ($sql === '') {
                continue;
            }
            $filtered = self::injectUserIdFilter($sql, (string) $userId);
            $rows = DB::select($filtered);
            $csv = self::toCsv($rows);
            $parsed->setData($i, $csv);
        }

        return $parsed;
    }
}

class FinanceAnalyzeResult
{
    public function __construct(protected array $queries) {}

    public static function parse(string $response): self
    {
        $lines = preg_split("/\r\n|\n|\r/", trim($response));
        array_shift($lines);
        $queries = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            [$sql, $reason] = explode(';', $line, 2);
            $queries[] = [
                'sql' => trim($sql),
                'reason' => trim($reason),
            ];
        }

        return new self($queries);
    }

    public function all(): array
    {
        return $this->queries;
    }

    public function sqls(): array
    {
        return array_map(fn ($q) => $q['sql'] ?? '', $this->queries);
    }

    public function reasons(): array
    {
        return array_map(fn ($q) => $q['reason'] ?? '', $this->queries);
    }

    public function setData(int $index, string $csv): void
    {
        if (! isset($this->queries[$index])) {
            return;
        }
        $this->queries[$index]['data'] = $csv;
    }

    public function dataOf(int $index): ?string
    {
        return $this->queries[$index]['data'] ?? null;
    }

    public function generateMessages(): array
    {
        return array_map(function ($q) {
            $reason = (string) ($q['reason'] ?? '');
            $data = (string) ($q['data'] ?? '');
            $content = '';
            if ($reason !== '' && $data !== '') {
                $content = $reason."\n\n".$data;
            } else {
                $content = $reason !== '' ? $reason : $data;
            }

            return [
                'role' => 'assistant',
                'content' => $content,
            ];
        }, $this->queries);
    }
}
