<?php

namespace Tests\Unit\Exception;

use App\Exceptions\Handler;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\TestCase;

class HandleTest extends TestCase
{
    /**
     * @return void
     */
    public function it_converts_an_exception_into_a_json_api_spec_error_response()
    {
        /** @var Handler $handler */
        $handler = app(Handler::class);
        $request = HttpRequest::create('/test', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');

        $exception = new \Exception('Test Exception');

        $response = $handler->render($request, $exception);
        TestResponse::fromBaseResponse($response)->assertJson([
            'errors' => [
                [
                    'title' => 'Exception',
                    'details' => 'Test exception',
                ]
            ]
        ]);
        $this->assertTrue(true);
    }
}
