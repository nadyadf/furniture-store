<?php

function isLogin(): bool
{
    return session()->has('usrid');
}
