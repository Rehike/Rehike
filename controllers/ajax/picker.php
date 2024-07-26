<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\Model\Picker\LocalePickerModel;
use \Rehike\Signin\API as SignIn;
use \Rehike\Signin\AuthManager;
use \Rehike\Signin\Cacher;
use \Rehike\TemplateManager;

/**
 * Controller for the account picker AJAX endpoint.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController
{
    public string $template = "ajax/picker";
    
    /** @override */
    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();

        if ($action == "language")
        {
            $this->getLanguagePicker();
        }
        else if ($action == "country")
        {
            
        }
        else if ($action == "safetymode")
        {
            
        }
        else
        {
            self::error();
        }
    }
    
    /** @override */
    public function doGeneralRender(): void
    {
        header("Content-Type: application/json");
        
        $obj = (object)["html" => null];
        $obj->html = TemplateManager::render();
        
        echo json_encode($obj);
    }
    
    private function getLanguagePicker(): void
    {
        Network::innertubeRequest(
            action: "account/account_menu",
            body: [
                "deviceTheme" => "DEVICE_THEME_SUPPORTED",
                "userInterfaceTheme" => "USER_INTERFACE_THEME_LIGHT"
            ]
        )->then(function ($response) {
            $data = $response->getJson();
            
            $menuPage = $this->findMenuPageFromIconId($data, "TRANSLATE");
            
            //\Rehike\Logging\DebugLogger::print(var_export($menuPage, true));
            
            if ($menuPage)
            {
                $this->yt->page = new LocalePickerModel(
                    type: LocalePickerModel::TYPE_LANGUAGE, 
                    data: $menuPage
                );
            }
            else
            {
                http_response_code(400);
                die('{errors:["Failed to find the page data."]}');
            }
        });
    }
    
    /**
     * Find an InnerTube account_menu page from the icon ID of the selector
     * button.
     */
    private function findMenuPageFromIconId(object $response, string $iconId): ?object
    {
        if (
            isset($response->actions[0]->openPopupAction->popup->multiPageMenuRenderer)
        )
        {
            $sectionsContainer = $response->actions[0]->openPopupAction->popup->multiPageMenuRenderer;
            
            if (isset($sectionsContainer->sections) && is_array($sectionsContainer->sections))
            foreach ($sectionsContainer->sections as $i => $section)
            {
                if (isset($section->multiPageMenuSectionRenderer))
                {
                    $sectionRenderer = $section->multiPageMenuSectionRenderer;
                    
                    if (isset($sectionRenderer->items) && is_array($sectionRenderer->items))
                    foreach ($sectionRenderer->items as $i2 => $item)
                    {
                        if (isset($item->compactLinkRenderer))
                        {
                            $itemRenderer = $item->compactLinkRenderer;
                            
                            if (isset($itemRenderer->icon->iconType))
                            {
                                if ($itemRenderer->icon->iconType == $iconId)
                                {
                                    return $this->getPageFromItemRenderer($itemRenderer);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Get an InnerTube account_menu page from the parent item renderer.
     */
    private function getPageFromItemRenderer(object $item): ?object
    {
        if (
            isset($item->serviceEndpoint->signalServiceEndpoint->actions[0]->getMultiPageMenuAction
                    ->menu->multiPageMenuRenderer)
        )
        {
            return $item->serviceEndpoint->signalServiceEndpoint->actions[0]->getMultiPageMenuAction
                ->menu->multiPageMenuRenderer;
        }
        
        return null;
    }
};