<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static self make()
 * @method static JsonResponse getResponse()
 * @method static self setStatus($status)
 * @method static self setMessage($status)
 * @method static self setResponseCode($status)
 * @method static self setExtraParams($status)
 * @method static self addExtraParam($status)
 * @method static self addExtraParams($status)
 *
 * @see PaymentResponseHelper
 */
class PaymentResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PaymentResponseHelper::class;
    }
}
