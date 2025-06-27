<?php

namespace Tests\Feature\Helpers;

use Illuminate\Http\Client\Response;
use Illuminate\Testing\TestResponse;

class FakeHttpTestResponse extends TestResponse
{
    public static function fromHttpClient(Response $response): self
    {
        return new self(
            response($response->body(),
            $response->status(),
            $response->headers())
        );
    }
}
