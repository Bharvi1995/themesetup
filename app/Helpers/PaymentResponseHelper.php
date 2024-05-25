<?php

namespace App\Helpers;

class PaymentResponseHelper
{
    public $status = '1';

    public $message = null;

    public $responseCode = 200;

    public $extraParams = [];

    const ERRORS = [
        '1' => ''
    ];

    public function __construct()
    {
    }

    public function make()
    {
        return $this;
    }

    public function getResponse()
    {
        $response = [
            'status' => $this->status,
            'message' => $this->message,
        ];

        if (! empty($this->extraParams)) {
            foreach ($this->extraParams as $key => $value) {
                $response[$key] = $value;
            }
        }

        return response()->json($response)->setStatusCode($this->responseCode);
    }

    /**
     * @param $status
     *
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param null $message
     *
     * @return self
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param int $responseCode
     *
     * @return self
     */
    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * @param array $extraParams
     *
     * @return self
     */
    public function setExtraParams(array $extraParams): self
    {
        $this->extraParams = $extraParams;

        return $this;
    }

    /**
     * @param array $extraParam
     *
     * @return self
     */
    public function addExtraParam(array $extraParam): self
    {
        $this->extraParams = array_push($this->extraParams, $extraParam);

        return $this;
    }

    /**
     * @param array $extraParams
     *
     * @return self
     */
    public function addExtraParams(array $extraParams): self
    {
        foreach ($extraParams as $extraParam) {
            $this->addExtraParam($extraParam);
        }

        return $this;
    }

}
