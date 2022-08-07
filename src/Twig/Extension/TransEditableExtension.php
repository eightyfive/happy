<?php

namespace Happy\Twig\Extension;

use Happy\ContenteditableService;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Benoit Sagols <benoit.sagols@gmail.com>
 */
class TransEditableExtension extends EditableExtension
{
    private $translator;

    public function __construct(ContenteditableService $content, TranslatorInterface $translator)
    {
        parent::__construct($content);

        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans_editable', array($this, 'editableTrans'))
        );
    }

    public function editableTrans($id, $domain = null, $mode = 'inline')
    {
        if (!$domain) {
            $domain = 'messages';
        }
        
        $content = $this->translator->trans($id, array(), $domain);
        $inputName = 'trans['.$domain.']['.$id.']';

        return $this->renderEditableNode($content, $inputName, 'text', $mode);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'trans_editable';
    }
}
