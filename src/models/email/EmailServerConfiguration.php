<?php

namespace models\email;

class EmailServerConfiguration
{
    public string $Server;
    public string $SenderAddress;
    public string $SenderName;
    public string $SenderPassword;
    public int $Port;
}
