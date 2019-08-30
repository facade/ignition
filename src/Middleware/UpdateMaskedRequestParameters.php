<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;

class UpdateMaskedRequestParameters
{

    public function handle(Report $report, $next)
    {
        if ($fields_to_mask = $this->getFieldsToMask()) {
            $context = $report->allContext();

            $this->maskProperties($fields_to_mask, $context);

            $this->maskQueryString($fields_to_mask, $context['request']['url']);

            $report->userProvidedContext($context);
        }

        return $next($report);
    }

    protected function getFieldsToMask(): array
    {
        return config('ignition.masked_request_parameters');
    }

    protected function maskProperties(array $fields_to_mask, array &$context)
    {
        $fields_to_mask = array_flip($fields_to_mask);

        foreach ($context as $key => &$value) {
            if (is_iterable($value)) {
                $this->maskProperties($fields_to_mask, $value);

                continue;
            }

            if (isset($fields_to_mask[$key])) {
                $this->maskProperty($value);
            }
        }
    }

    protected function maskProperty(&$value, &$changed = null)
    {
        if (is_iterable($value)) {
            foreach ($value as &$_value) {
                $this->maskProperty($_value, $changed);
            }

            return;
        }

        $initial_value = $value;

        switch (gettype($this->cast($value))) {
            case "boolean":
                $value = $this->randomiseValueFromArray([true, false]);

                break;

            case "integer":
                $value = $this->randomInteger(strlen($value));

                break;

            case "double":
                $value = $this->randomFloat(strlen((int) $value));

                break;

            case "string":
                $value = $this->randomString(strlen($value));

                break;

            case "NULL":
            case "unknown type":
                $value = null;

                break;

        }

        if ($changed !== null && $initial_value !== $value) {
            $changed = true;
        }
    }

    protected function maskQueryString(array $fields_to_mask, string &$url)
    {
        $url_parts = parse_url($url);
        parse_str($url_parts['query'], $query_strings);

        if ($url_parts['query']) {
            $changes = false;
            foreach ($fields_to_mask as $field) {
                if (isset($query_strings[$field])) {
                    $this->maskProperty($query_strings[$field], $changes);
                }
            }

            if ($changes) {
                $url = $url_parts['scheme'] . '://' . $url_parts['host'] . '?' . http_build_query($query_strings);
            }
        }
    }

    protected function randomiseValueFromArray(array $possible_values)
    {
        return $possible_values[array_rand($possible_values)];
    }

    protected function randomString($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    protected function randomFloat($length): string
    {
        return $this->randomInteger($length - 2) - rand (0, 100) / 100;
    }

    protected function randomInteger($length): int
    {
        return rand(1, (int) str_pad('', $length, 9));
    }

    protected function cast($value)
    {
        if (is_numeric($value)) {
            if ((int) $value == $value) {
                return (int) $value;
            }

            return (float) $value;
        }

        return $value;
    }
}
