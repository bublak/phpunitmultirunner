<?php

interface IEngine {
    public function runUnits(array $tests, array $options=null);
}
