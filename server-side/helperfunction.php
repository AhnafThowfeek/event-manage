<?php

// UUID version 4
function generateUUID() {
    $data = random_bytes(16); // Generate 16 bytes (128 bits) of random data
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}