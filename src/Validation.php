<?php

namespace DeployHuman\PHPImageHandler;

class Validation

{

    public static function base64(string $data): bool
    {
        return (bool) mb_ereg_match('^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)?$', $data);
    }
}
