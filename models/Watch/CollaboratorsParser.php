<?php
namespace Rehike\Model\Watch;

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
    
    public function __construct(WatchBakery $bakery)
    {
        $this->bakery = $bakery;
        $this->rootData = $this->findRootData();
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
}