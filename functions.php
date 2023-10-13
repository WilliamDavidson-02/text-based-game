<?php

declare(strict_types=1);

function createErrorMsg(string $msg)
{
    $_SESSION['errorMessage'] = $msg;
}
