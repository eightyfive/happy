<?php
namespace Eyf\Happy\Editor;

use Eyf\Modelify\Repository;

class ModelifyEditor implements ContentEditorInterface
{
    protected $repos = array();

    public function getKey()
    {
        return 'class';
    }

    public function save(array $data)
    {
        foreach ($data as $entityName => $entities) {

            $repo = $this->getRepository($entityName);

            foreach ($entities as $id => $attrs) {
                $entity = $repo->find($id);
                $entity->setAttributes($attrs);

                // Persist
                $repo->save($entity);
            }
        }
    }

    public function addRepository(Repository $repo)
    {
        $this->repos[$repo->getEntityClassName()] = $repo;
    }

    protected function getRepository($entityName)
    {
        if (!isset($this->repos[$entityName])) {
            throw new \RuntimeException('No repository registered for entity: '.$entityName);
        }

        return $this->repos[$entityName];
    }

}