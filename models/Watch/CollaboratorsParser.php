<?php
namespace Rehike\Model\Watch;

use Rehike\Model\Common\MCollaborator;

/**
 * Collects information on all collaborators on a YouTube video and centralises
 * the information into a single source.
 * 
 * Because this information is scattered throughout the InnerTube response and
 * needs to be accessed in multiple cases, this class makes it easier to keep
 * track of this information.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CollaboratorsParser
{
    private WatchBakery $bakery;
    private object $rootData;
    
    /**
     * @var MCollaborator[]
     */
    private array $collaborators = [];
    
    public function __construct(WatchBakery $bakery)
    {
        $this->bakery = $bakery;
        $this->rootData = $this->findRootData();
        
        foreach ($this->rootData->customContent->listViewModel->listItems as $listItem)
        {
            $this->collaborators[] = new MCollaborator($listItem->listItemViewModel);
        }
    }
    
    private function findRootData(): object
    {
        return $this->bakery->secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint
            ->showDialogCommand->panelLoadingStrategy->inlineContent->dialogViewModel;
    }
    
    public function getRootData(): object
    {
        return $this->rootData;
    }
    
    /**
     * @return MCollaborator[]
     */
    public function getCollaborators(): array
    {
        return $this->collaborators;
    }
}