<?php
namespace Rehike;

/**
 * Parses the InnerTube view model system.
 *
 * The view model system is being increasingly commonly used in InnerTube as of 2024, and
 * really sucks to parse.
 *
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class ViewModelParser
{
    private object $innertubeData;
    private array $entities = [];

    public function __construct(object $innertubeData)
    {
        $this->innertubeData = $innertubeData;
        $this->enumerateEntities();
    }

    private function enumerateEntities(): void
    {
        $entities = $this->innertubeData->frameworkUpdates->entityBatchUpdate->mutations;

        foreach ($entities as $entity)
        {
            $this->entities[$entity->entityKey] = &$entity;
        }
    }

    /**
     * Gets a map of the view model entities.
     */
    public function getViewModelEntities(object $viewModel, array $dataMap): array
    {
        $out = [];

        foreach ($dataMap as $propertyName => $accessorName)
        {
            if (isset($viewModel->{$propertyName}))
            {
                $out[$accessorName] = $viewModel->{$propertyName};
            }
            else
            {
                $out[$accessorName] = null;
            }
        }

        return $out;
    }
}