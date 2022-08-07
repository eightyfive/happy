<?php

namespace Eyf\Happy\Twig\Extension;

use Happy\ContenteditableService;

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

    protected function renderEditableNode($content, $inputName, $type, $typeMode = null, $attributes = array())
    {
        if (!$this->content->isEditable()) {
            echo $content;
            return;
        }

        $tagName = 'span';

        if ($type === 'text') {
            $tagName = ($typeMode === 'inline') ? 'span' : 'div';
        }

        $node = '<'.$tagName;
        
        $attrs['editable'] = null;
        $attrs['editable-name'] = $inputName;
        $attrs['editable-'.$type] = $typeMode;

        $attrs = array_merge($attributes, $attrs);

        foreach ($attrs as $name => $value) {
            $node .= ' data-'.$name.($value ? '=\''.$value.'\'' : '');
        }

        $node .= '>';
        $node .= $content;
        $node .= '</'.$tagName.'>';

        echo $node;
    }
}
