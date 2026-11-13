<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionAlreadyActiveException extends Exception
{
    public function __construct(
        string $message = 'El cliente ya cuenta con una suscripción activa.'
    ) {
        parent::__construct($message);
    }

    public function render(
        Request $request
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => [
                    'client_id' => [
                        $this->getMessage(),
                    ],
                ],
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
