<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    protected function apiResponse($result, $status, $message, $data = null): JsonResponse
    {
        $response = [
            'result' => $result,
            'status' => (int) $status,
            'message' => $message,
            'data' => $data,
        ];


        return response()->json($response, $this->httpCode($status));
    }

    protected function apiResponseList($result, $status, $message, $data = [], $totalData = null, $perPage = 10, $currentPage = null): JsonResponse
    {
        $response = [
            'result' => $result,
            'status' =>  (int) $status,
            'message' => $message,
            'data' => $data,
            'total_data' => (int)  $totalData,
            'total_page' => (int)  ceil($totalData / $perPage),
            'page' => (int)  $currentPage,

        ];

        return response()->json($response, $this->httpCode($status));
    }

    private function httpCode($statusCode)
    {
        $httpStatus = 400;
        switch ($statusCode) {
            case $statusCode === 200:
                $httpStatus = Response::HTTP_OK;
                break;
            case $statusCode === 400:
                $httpStatus = Response::HTTP_BAD_REQUEST;
                break;
            case $statusCode === 404:
                $httpStatus = Response::HTTP_NOT_FOUND;
                break;
            case $statusCode === 500:
                $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $httpStatus;
    }

    protected function getImageExtensionFromBase64($base64Image)
    {
        $data = explode(',', $base64Image);

        if (count($data) > 1) {
            $extensionData = explode(';', $data[0]);
            $extension = explode('/', $extensionData[0])[1];
            return $extension;
        }

        return null;
    }
    protected function getBase64($base64String)
    {
        $base64Value = substr($base64String, strpos($base64String, ',') + 1);
        return $base64Value;
    }
}
