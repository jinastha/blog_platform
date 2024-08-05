<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function response($data, $code, $context)
    {
        return response()->json($data, $code);
    }

    public function getSuccessResponse($responseCode, $context, $message = null, $headers = [], $header = [])
    {
        $codeType = config('messages.code_type.success');
        $resourceType = config('messages.resource_code.' . $responseCode['resource_type']);

        $returnData['message_code'] = $messageCode = $codeType | $resourceType | $responseCode['status'];

        if (is_null($message)) {
            $string = ucwords(str_replace("_", " ", $responseCode['resource_type']));

            $message = str_replace("%s", $string, config("messages.status_code." . $responseCode['status'] . '.' . $responseCode['method']));
        }

        $returnData['message'] = $message;

        if (isset($responseCode['result'])) {
            $returnData['result'] = $responseCode['result'];
        }


        if ($headers) {

            return response()->json($returnData, $responseCode['status'])->header($headers['key'], $headers['value']);
        }

        if ($header) {
            return response()->json($returnData, $responseCode['status'])->withHeaders($header);
        }
        return response()->json($returnData, $responseCode['status']);
    }

    public function getErrorResponse($responseCode, $context, $message = null, $headers = [])
    {
        $codeType = config('messages.code_type.error');
        $resourceType = config('messages.resource_code.' . $responseCode['resource_type']);

        $returnData['message_code'] = $messageCode = $codeType | $resourceType | $responseCode['status'];

        if (is_null($message)) {
            $string = ucwords(str_replace("_", " ", $responseCode['resource_type']));

            $message = str_replace("%s", $string, config("messages.status_code." . $responseCode['status'] . '.message'));
        }

        $returnData['message'] = $message;


        if (isset($responseCode['result'])) {
            $returnData['result'] = $responseCode['result'];
        }


        Log::error($context, [
            "status" => $responseCode['status'],
            "message_code" => $messageCode,
            "message" => isset($responseCode['actual_message']) ? $responseCode['actual_message'] : (is_array($message) || is_object($message) ? serialize($message) : $message),
            "result" => isset($returnData['result']) ? serialize($returnData['result']) : ''
        ]);


        if ($headers) {

            return response()->json($returnData, $responseCode['status'])->header($headers['key'], $headers['value']);
        }
        return response()->json($returnData, $responseCode['status']);
    }

    public function message($message, $code, $context, $messageOverride = '')
    {
        if ($code >= 500) {
            Log::error($context, [
                "status" => $code,
                "message" => is_array($message) || is_object($message) ? serialize($message) : $message
            ]);
        }
        $message = isset($message) && $messageOverride != '' ? $messageOverride : $message;

        return response()->json(['message' => $message], $code);
    }

    public function limit(Request $request)
    {
        try {
            $this->validate($request, [
                "limit" => "required|integer|min:1"
            ]);
            $limit = $request->limit;
        } catch (\Exception $ex) {
            $limit = $request->get("limit", config('config.response_rows'));
        }
        return $limit;
    }
}
