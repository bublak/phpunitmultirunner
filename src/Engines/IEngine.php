<?php

namespace bublak\phpunitmultirunner\Engines;

interface IEngine {
    public function runUnits(array $tests, array $options=null);
}
