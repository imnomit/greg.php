<?php

namespace nomit\Client\Support;

final class DnsCache implements DnsCacheInterface
{

    public array $hostnames = [];

    public array $removals = [];

    public array $evictions = [];

}