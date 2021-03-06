<?php

namespace App\Http\Controllers;

use App\Services\JsonResponseFormatter;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;

abstract class RestController extends BaseController
{

    /**
     * @param array $schemas
     * @return EncoderInterface
     */
    protected function createEncoder(array $schemas)
    {
        $encoderOptions = new EncoderOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return Encoder::instance($schemas, $encoderOptions);
    }

    protected function withJson(JsonResponseFormatter $jsonResponse, $status = 200)
    {
        return response()->json($jsonResponse->toArray(), $status);
    }

    protected function withStatus($status)
    {
        return response()->json([], $status);
    }

    protected function withJsonApi($json, $status = 200)
    {
        return response($json, $status, ['Content-Type' => 'application/json']);
    }

    protected function validateRequest(Request $request, array $validationRules, array $newRules = [])
    {
        $this->validate($request, array_merge($validationRules, $newRules));
    }

}
