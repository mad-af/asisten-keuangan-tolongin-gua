<?php

namespace Tests\Unit\Services;

use App\Services\ChatRequest;
use App\Services\ChatResponse;
use PHPUnit\Framework\TestCase;

class KolosalApiClientTest extends TestCase
{
    public function test_chat_request_from_array_requires_messages(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ChatRequest::fromArray(['max_tokens' => 10]);
    }

    public function test_chat_response_message_content_returns_null_when_no_choices(): void
    {
        $res = ChatResponse::success(200, ['choices' => []]);
        $this->assertNull($res->messageContent());
    }
}

