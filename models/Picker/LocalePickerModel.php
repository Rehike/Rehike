<?php
namespace Rehike\Model\Picker;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\Util\ParsingUtils;

/**
 * Used for locale (language and country) pickers.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class LocalePickerModel extends PickerModel
{
    public const TYPE_LANGUAGE = 0;
    public const TYPE_COUNTRY = 1;
    
    private int $type = 0;
    private MPickerSection $section;
    
    public function __construct(int $type, object $data)
    {
        $i18n = i18n::getNamespace("picker");
        
        $this->type = $type;
        $this->header = new MPickerHeader();
        
        if ($type == self::TYPE_LANGUAGE)
        {
            $this->header->titleText = $i18n->get("languageTitle");
            $this->header->notesText = $i18n->get("languageSubtitle");
            $this->header->closeButtonTargetId = "yt-picker-language-button";
            $this->formAction = "/picker_ajax?action_update_language=1";
        }
        else if ($type == self::TYPE_COUNTRY)
        {
            
        }
        
        $this->section = new MPickerSection();
        $this->addSection($this->section);
        
        $this->parseFromInnertube($data);
    }
    
    private function parseFromInnertube(object $data): void
    {
        if (isset($data->sections[0]->multiPageMenuSectionRenderer->items))
        {
            $items = $data->sections[0]->multiPageMenuSectionRenderer->items;
            
            foreach ($items as $i => $item)
            {
                if (isset($item->compactLinkRenderer))
                {
                    $renderer = $item->compactLinkRenderer;
                    $finalItem = $this->itemFromInnertube($renderer);
                    
                    if ($finalItem)
                    {
                        $this->section->addItem($finalItem);
                    }
                    else
                    {
                        throw new \Exception("Unable to build final item");
                    }
                }
                else
                {
                    throw new \Exception("No compact link renderer");
                }
            }
        }
        else
        {
            throw new \Exception("No items");
        }
    }
    
    private function itemFromInnertube(object $data): ?object
    {
        $result = new MPickerItemButton();
        
        $name = match ($this->type)
        {
            self::TYPE_LANGUAGE => "hl",
            self::TYPE_COUNTRY  => "gl",
        };
        
        // Finding the ID is a bit of a mess as usual...
        $id = "";
        if ($this->type == self::TYPE_LANGUAGE)
        {
            try
            {
                $id = @$data->serviceEndpoint->signalServiceEndpoint->actions[0]
                    ->selectLanguageCommand->hl;
            }
            catch (\Throwable $e) {}
        }
        else if ($this->type == self::TYPE_COUNTRY)
        {
            
        }
        
        $result->title = ParsingUtils::getText($data->title);
        $result->name = $name;
        $result->value = $id;
        
        return $result;
    }
}