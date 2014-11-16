<?php
namespace Eyf\Happy\Editor;

interface EditorInterface
{
    public function getKey();
    public function save(array $data);
}