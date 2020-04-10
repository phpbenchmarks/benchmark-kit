<?php

declare(strict_types=1);

namespace App\Command\Behavior;

trait GetBodyFromUrl
{
    /** @return $this */
    protected function getBodyFromUrl(string $url, bool $assertIs200 = true): ?string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);

        if ($body === false) {
            throw new \Exception("Error while calling $url: " . curl_error($curl) . '.');
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($assertIs200 && $httpCode !== 200) {
            throw new \Exception('Http code should be 200 but is ' . $httpCode . '.');
        }

        return $body;
    }
}
