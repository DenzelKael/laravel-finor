<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpiredSubscriptionException extends Exception
{
    public function __construct(
        string $message = 'No se puede registrar el pago porque la suscripción está vencida.'
    ) {
        parent::__construct($message);
    }

    /**
     * Render the exception as an appropriate HTTP response.
     */
    public function render(
        Request $request
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => [
                    'subscription_id' => [
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