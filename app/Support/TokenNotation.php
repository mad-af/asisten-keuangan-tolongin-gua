<?php

namespace App\Support;

use HelgeSverre\Toon\EncodeOptions;
use HelgeSverre\Toon\Toon;

/**
 * TOON vs JSON (contoh ringkas)
 *
 * JSON:
 * {
 *   "users": [
 *     {"id": 1, "name": "Alice", "role": "admin"},
 *     {"id": 2, "name": "Bob", "role": "user"}
 *   ]
 * }
 *
 * TOON:
 * users[2]{id,name,role}:
 *   1,Alice,admin
 *   2,Bob,user
 *
 * JSON:
 * { "id": 123, "name": "Ada", "active": true }
 *
 * TOON:
 * id: 123
 * name: Ada
 * active: true
 *
 * JSON:
 * ["a","b","c"]
 *
 * TOON:
 * [3]: a,b,c
 */
class TokenNotation
{
    public static function encode(mixed $data, ?EncodeOptions $options = null): string
    {
        return Toon::encode($data, $options);
    }

    public static function decode(string $notation): mixed
    {
        return Toon::decode($notation);
    }

    public static function compact(mixed $data): string
    {
        $options = new EncodeOptions(indent: 0);

        return Toon::encode($data, $options);
    }

    public static function readable(mixed $data, int $indent = 4): string
    {
        $options = new EncodeOptions(indent: $indent);

        return Toon::encode($data, $options);
    }

    public static function delimiter(mixed $data, string $delimiter = ','): string
    {
        $options = new EncodeOptions(delimiter: $delimiter);

        return Toon::encode($data, $options);
    }
}
