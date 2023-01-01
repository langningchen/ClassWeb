<?php
while (count($_COOKIE)) {
    setcookie(array_key_last($_COOKIE), "", time() - 1);
    array_pop($_COOKIE);
}
echo "重置成功";
