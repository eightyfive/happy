<?php
namespace Eyf\Happy\Editor;

interface ContentEditorInterface
{
    public function getKey();
    public function save(array $data);
}