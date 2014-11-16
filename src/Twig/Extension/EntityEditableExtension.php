<?php

namespace Eyf\Happy\Twig\Extension;

use Eyf\Happy\ContenteditableService;
use Eyf\Happy\Behavior\Editable;

/**
 * @author Benoit Sagols <benoit.sagols@gmail.com>
 */
class EntityEditableExtension extends EditableExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('editable', array($this, 'editableTextAttr')),
            new \Twig_SimpleFunction('editable_datetime', array($this, 'editableDatetimeAttr'))
        );
    }

    protected function getContent(Editable $entity, $attr)
    {
        return $entity->getAttribute($attr);
    }

    protected function getInputName(Editable $entity, $attr)
    {
        return 'entity['.get_class($entity).']['.$entity->getId().']['.$attr.']';
    }

    public function editableDatetimeAttr(Editable $entity, $attr, $format = null)
    {
        $contentRaw = $this->getContent($entity, $attr);
        $inputName = $this->getInputName($entity, $attr);

        if ($format) {
            $date = new \Datetime($contentRaw);
            $content = $date->format($format);
        } else {
            $content = $contentRaw;
        }

        $this->renderEditableNode($content, $inputName, 'datetime', $contentRaw);
    }

    public function editableTextAttr(Editable $entity, $attr, $mode = 'inline')
    {
        $content = $this->getContent($entity, $attr);
        $inputName = $this->getInputName($entity, $attr);

        $this->renderEditableNode($content, $inputName, 'text', $mode);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'editable';
    }
}
