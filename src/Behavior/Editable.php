<?php
namespace Eyf\Happy\Behavior;

interface Editable
{
    public function getId();
    public function getAttribute($key);
}