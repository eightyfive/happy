<?php
namespace Eyf\Happy\Editor;

use Symfony\Component\Translation\TranslatorInterface;

class TransEditor implements ContentEditorInterface
{
    protected $translator;
    protected $resources = array();

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getKey()
    {
        return 'trans';
    }

    public function addResource($format, $filename, $locale, $domain = null)
    {
        $this->resources[] = array($format, $filename, $locale, $domain);
    }

    public function save(array $data)
    {
        foreach ($data as $domain => $translations) {
            $this->saveDomain($domain, $translations);
        }
    }

    protected function saveDomain($domain, array $translations)
    {
        $locale = $this->translator->getLocale();

        foreach ($this->resources as $resource) {
            list ($rFormat, $rFilename, $rLocale, $rDomain) = $resource;

            if ($rFormat === 'json' && $rLocale === $locale && $rDomain === $domain) {

                $current = json_decode(file_get_contents($rFilename), true);

                // (Small) Security... Do not proceed if more translations than current.
                // if (count($translations) <= count($current)) {
                    $translations = array_replace_recursive($current, $translations);
                    file_put_contents($rFilename, json_encode($translations, JSON_PRETTY_PRINT));
                // }
                break;
            }
        }
    }
}