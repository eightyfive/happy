<?php

namespace Eyf\Happy\Twig\Extension;

use Eyf\Happy\ContenteditableService;

/**
 * @author Benoit Sagols <benoit.sagols@gmail.com>
 */
abstract class EditableExtension extends \Twig_Extension
{
    protected $content;

    public function __construct(ContenteditableService $content)
    {
        $this->content = $content;
    }

    protected function renderEditableNode($content, $inputName, $type, $typeMode = null)
    {
        if (!$this->content->isEditable()) {
            echo $content;
            return;
        }

        $tagName = 'span';

        if ($type === 'content') {
            $tagName = ($typeMode === 'inline') ? 'span' : 'div';
        }

        $node = '<'.$tagName;

        $node .= $this->getAttributes($inputName, $type, $typeMode);
        $node .= '>';
        $node .= $content;
        $node .= '</'.$tagName.'>';

        echo $node;
    }

    protected function renderEditableAttributes($inputName, $type)
    {
        if (!$this->content->isEditable()) {
            return;
        }

        echo $this->getAttributes($inputName, $type);
    }

    protected function getAttributes($inputName, $type, $typeMode = null)
    {
        $attrs = array();
        $attrs['editable'] = $type;
        $attrs['editable-name'] = $inputName;
        if ($typeMode) {
            $attrs['editable-'.$type] = $typeMode;
        }

        $str = '';
        foreach ($attrs as $name => $value) {
            $str .= ' data-'.$name.($value ? '=\''.$value.'\'' : '');
        }

        return $str;
    }
}
