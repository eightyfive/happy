<?php

namespace Happy\Twig\Extension;

use Happy\ContenteditableService;
use Happy\Behavior\Editable;

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
            new \Twig_SimpleFunction('editable', array($this, 'editableText')),
            new \Twig_SimpleFunction('editable_attrs', array($this, 'editableAttrs')),
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

    public function editableAttrs(Editable $entity, $attr, $type = null)
    {
        $inputName = $this->getInputName($entity, $attr);

        $this->renderEditableAttributes($inputName, $type);
    }

    public function editableText(Editable $entity, $attr, $mode = 'inline')
    {
        $content = $this->getContent($entity, $attr);
        $inputName = $this->getInputName($entity, $attr);

        $this->renderEditableNode($content, $inputName, 'content', $mode);
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
