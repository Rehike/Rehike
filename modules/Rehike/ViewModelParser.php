<?php
namespace Rehike;

/**
 * Parsing utilities for InnerTube's view model system.
 * 
 * As of late January 2024, a new MVVM system is being used across InnerTube,
 * which has a few significant differences from how InnerTube previously worked.
 * 
 * One notable difference with view models is that they have information split
 * between their base objects and mutation entities. Previous InnerTube models,
 * which were called "renderers", were flat: all data existed in a single
 * renderer structure.
 * 
 * Unfortunately, view models aren't particularly consistent about how they wish
 * to separate their data. For example, comments are almost entirely stored in
 * mutation entities across multiple different keys, whereas the segmented
 * like/dislike button model also even contains a copy of the entity data in the
 * view model.
 * 
 * This class mostly exists to steamline grabbing data from mutation entities,
 * so it may not be entirely necessary for all view models.
 *
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ViewModelParser
{
    /**
     * Points to the raw data of the InnerTube view model.
     */
    private object $viewModelData;

    /**
     * Points to the raw data of the InnerTube framework updates, which is where
     * mutation entities are stored.
     */
    private object $frameworkUpdates;

    /**
     * Stores a map of mutation entity references, for efficient access.
     */
    private array $entities = [];

    public function __construct(object $viewModelData, object $frameworkUpdates)
    {
        $this->viewModelData = $viewModelData;
        $this->frameworkUpdates = $frameworkUpdates;
        $this->enumerateEntities();
    }

    private function enumerateEntities(): void
    {
        $entities = $this->frameworkUpdates->entityBatchUpdate->mutations;

        foreach ($entities as &$entity)
        {
            $this->entities[$entity->entityKey] = &$entity;
        }
    }

    /**
     * Gets a map of the view model entities.
     */
    public function getViewModelEntities(array $dataMap): array
    {
        $out = [];

        foreach ($dataMap as $propertyName => $accessorName)
        {
            if (isset($this->viewModelData->{$propertyName}))
            {
                $out[$accessorName] = $this->entities[$this->viewModelData->{$propertyName}];
            }
            else
            {
                $out[$accessorName] = null;
            }
        }

        return $out;
    }
}