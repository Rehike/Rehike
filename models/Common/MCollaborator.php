<?php
namespace Rehike\Model\Common;

use Rehike\Util\ParsingUtils;
use Rehike\Util\ExtractUtils;
use Rehike\SignInV2\SignIn;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\ViewModelParser;

/**
 * Intermediate parsing state for collaborator information from the collaborator dialog.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MCollaborator
{
    public object $rawData;
    
    public ?string $name = null;
    public bool $verified = false;
    public ?string $avatarUrl = null;
    public ?string $subscriberCount = null;
    public ?object $navigationEndpoint = null;
    
    public function __construct(object $listItem)
    {
        $this->rawData = $listItem;
        
        $this->name = ParsingUtils::getText($listItem->title);
        $this->avatarUrl = $listItem->leadingAccessory->avatarViewModel->image->sources[0]->url;
        
        if (isset($listItem->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource))
        {
            // XXX(isabella): For now, we will just assume any case like this means there's a badge. If
            // this has some false positives, then uncomment the below condition (and expand it with
            // other cases)
            $attachment = $listItem->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource;
            //if ($attachment->imageName == "CHECK_CIRCLE_FILLED")
            //{
                $this->verified = true;
            //}
        }
        
        if (isset($listItem->title->commandRuns[0]))
        {
            // This data is in commandRuns on watch page.
            $this->navigationEndpoint = $listItem->title->commandRuns[0]->onTap->innertubeCommand;
        }
        else if (isset($listItem->rendererContext->commandContext->onTap->innertubeCommand))
        {
            // Browse video renderers:
            $this->navigationEndpoint = $listItem->rendererContext->commandContext->onTap->innertubeCommand;
        }
        
        // Get subscription count:
        $baseSubtitle = ParsingUtils::getText($listItem->subtitle);
        $subtitleParts = explode("•", $baseSubtitle);
        
        $this->subscriberCount = null;
        
        if (isset($subtitleParts[1]))
        {
            $formattedSubscriberCount = trim($subtitleParts[1]);
            $this->subscriberCount = ExtractUtils::isolateSubCnt($formattedSubscriberCount);
        }
    }
    
    /**
     * Builds a subscription actions renderer from the subscribe button information from
     * the InnerTube dialog.
     */
    public function buildSubscriptionActions(object $frameworkUpdates, bool $branded = true): ?MSubscriptionActions
    {
        // Build the subscription button:
        if (!SignIn::isSignedIn())
        {
            return MSubscriptionActions::signedOutStub($this->subscriberCount, $branded);
        }
        else if (isset($firstItem->rawData->trailingButtons->buttons[0]->subscribeButtonViewModel))
        {
            $viewModel = $firstItem->rawData->trailingButtons->buttons[0]->subscribeButtonViewModel;
            $viewModelParser = new ViewModelParser($viewModel, $frameworkUpdates);
            
            return MSubscriptionActions::fromViewModel(
                $viewModel, $viewModelParser, $this->subscriberCount, $branded
            );
        }
        
        return null;
    }
}