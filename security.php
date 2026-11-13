<?php

function app_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sanitize_input($value)
{
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = sanitize_input($item);
        }
        return $value;
    }

    if (is_string($value)) {
        return trim(stripslashes($value));
    }

    return $value;
}
