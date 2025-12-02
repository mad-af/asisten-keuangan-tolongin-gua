<?php

namespace App\Support;

class TokenNotation
{
    public static function encode(array $data): string
    {
        $pairs = [];
        foreach ($data as $key => $value) {
            $pairs[] = self::encodePair((string) $key, $value);
        }
        return implode(';', $pairs);
    }

    public static function decode(string $notation): array
    {
        $notation = trim($notation);
        if ($notation === '') return [];
        $pairs = self::splitPairs($notation);
        $out = [];
        foreach ($pairs as $pair) {
            $kv = self::splitKeyValue($pair);
            if ($kv === null) continue;
            [$key, $raw] = $kv;
            $out[$key] = self::decodeValue($raw);
        }
        return $out;
    }

    protected static function encodePair(string $key, mixed $value): string
    {
        return $key . ':' . self::encodeValue($value);
    }

    protected static function encodeValue(mixed $value): string
    {
        if (is_string($value)) {
            return '"' . self::escapeString($value) . '"';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null) {
            return 'null';
        }
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected static function decodeValue(string $raw): mixed
    {
        $raw = trim($raw);
        if ($raw === '') return null;
        if ($raw === 'true') return true;
        if ($raw === 'false') return false;
        if ($raw === 'null') return null;
        if (self::isQuoted($raw)) return self::unescapeString(substr($raw, 1, -1));
        if (self::looksJson($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }
        if (preg_match('/^-?\d+$/', $raw)) return (int) $raw;
        if (preg_match('/^-?\d+\.\d+$/', $raw)) return (float) $raw;
        return $raw;
    }

    protected static function splitPairs(string $notation): array
    {
        $parts = [];
        $buf = '';
        $depthBraces = 0;
        $depthBrackets = 0;
        $inString = false;
        for ($i = 0; $i < strlen($notation); $i++) {
            $ch = $notation[$i];
            if ($ch === '"') {
                $prev = $i > 0 ? $notation[$i-1] : null;
                if ($prev !== '\\') $inString = !$inString;
                $buf .= $ch;
                continue;
            }
            if (!$inString) {
                if ($ch === '{') $depthBraces++;
                elseif ($ch === '}') $depthBraces--;
                elseif ($ch === '[') $depthBrackets++;
                elseif ($ch === ']') $depthBrackets--;
                elseif ($ch === ';' && $depthBraces === 0 && $depthBrackets === 0) {
                    $parts[] = trim($buf);
                    $buf = '';
                    continue;
                }
            }
            $buf .= $ch;
        }
        if (trim($buf) !== '') $parts[] = trim($buf);
        return $parts;
    }

    protected static function splitKeyValue(string $pair): ?array
    {
        $pos = strpos($pair, ':');
        if ($pos === false) return null;
        $key = trim(substr($pair, 0, $pos));
        $val = trim(substr($pair, $pos + 1));
        if ($key === '') return null;
        return [$key, $val];
    }

    protected static function isQuoted(string $raw): bool
    {
        return strlen($raw) >= 2 && $raw[0] === '"' && $raw[strlen($raw)-1] === '"';
    }

    protected static function looksJson(string $raw): bool
    {
        $c = $raw[0];
        return $c === '{' || $c === '[';
    }

    protected static function escapeString(string $s): string
    {
        return str_replace(['\\', '"', "\n", "\r", "\t"], ['\\\\', '\"', '\\n', '\\r', '\\t'], $s);
    }

    protected static function unescapeString(string $s): string
    {
        $s = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $s);
        $s = str_replace(['\\"'], ['"'], $s);
        $s = str_replace(['\\\\'], ['\\'], $s);
        return $s;
    }
}

