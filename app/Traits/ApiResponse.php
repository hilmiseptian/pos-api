<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
  protected function respondWithList(AnonymousResourceCollection $collection): JsonResponse
  {
    $raw = $collection->response(request())->getData(true);

    if (isset($raw['meta']['total'])) {
      return response()->json([
        'data' => $raw['data'],
        'meta' => [
          'current_page' => $raw['meta']['current_page'],
          'last_page'    => $raw['meta']['last_page'],
          'per_page'     => $raw['meta']['per_page'],
          'total'        => $raw['meta']['total'],
        ],
      ]);
    }

    return response()->json(['data' => $raw['data']]);
  }

  protected function respondWithItem(
    JsonResource $resource,
    string $message = null,
    int $status = 200
  ): JsonResponse {
    $payload = ['data' => $resource];

    if ($message !== null) {
      $payload['message'] = $message;
    }

    return response()->json($payload, $status);
  }

  protected function respondWithMessage(string $message, int $status = 200): JsonResponse
  {
    return response()->json(['message' => $message], $status);
  }
}
