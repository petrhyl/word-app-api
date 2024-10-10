<?php

namespace config;

use DateTime;
use WebApiCore\Exceptions\ApplicationException;
use WebApiCore\Http\HttpResponse;
use ErrorException;
use Throwable;
use utils\Constants;

class ErrorHandler
{
    public static function handleException(Throwable $exception): void
    {
        $statusCode = 500;
        $errors = [
            self::formatExceptionToLog($exception)
        ];
        $errorResponse = ['message' => 'Something went wrong.'];

        if ($exception::class === ApplicationException::class) {
            $appException = self::getApplicationExceptionObject($exception);
            $statusCode = $appException->getHttpStatusCode();
            $errorResponse['message'] = $appException->getMessage();
            $innerErrors = $appException->getInnerErrors();
            if (!empty($innerErrors)) {
                foreach ($innerErrors as $key => $value) {
                    $errors[] = [$key => $value];
                }
                $errorResponse = array_merge($errorResponse, $innerErrors);
            }
        }

        self::logErrors($errors);

        $response = new HttpResponse(null, $statusCode, $errorResponse);
        $response->send();
        die();
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    private static function getApplicationExceptionObject(Throwable $exception): ApplicationException
    {
        return $exception;
    }

    public static function logErrors(array $errors): void
    {
        $filename = Constants::rootDir() . '/logs/log_' . date('ymd') . '.json';
        $file = fopen($filename, 'a');

        if ($file) {
            $data = json_encode($errors, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);
            $data .= ',';

            $attempts = 0;
            while (!flock($file, LOCK_EX) && $attempts < 20) {
                $attempts++;
                usleep(100);
            }

            fwrite($file, $data);

            fclose($file);
        }
    }

    public static function formatExceptionToLog(Throwable $exception): array
    {
        $previous = $exception->getPrevious();

        return [
            "time" => date(DateTime::ATOM),
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
            "stackTrace" => $exception->getTraceAsString(),
            "previous" => empty($previous) ? null : self::formatExceptionToLog($previous)
        ];
    }
}
