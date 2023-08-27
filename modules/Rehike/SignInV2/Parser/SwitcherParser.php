<?php
namespace Rehike\SignInV2\Parser;

use Rehike\SignInV2\{
    Builder\SessionInfoBuilder,
    Parser\Exception\UnfinishedParsingException
};

/**
 * Parses and retrieves information from the account switcher, requested at
 * /getAccountSwitcherEndpoint and outputs it into the session info builder.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SwitcherParser
{
    /**
     * A reference to the builder to which we are to supply information.
     */
    private SessionInfoBuilder $builder;

    /**
     * The account switcher response, JSON decoded.
     */
    private object $response;

    /**
     * Used in order to determine if the parser has ran yet.
     */
    private bool $hasParsed = false;

    /**
     * Stores the number of Google Accounts accessible by the user.
     */
    private int $numberOfGoogleAccounts = 0;

    /**
     * Stores temporary state about the current Google Account, which is fed
     * to the builder.
     */
    private array $googleAccounts = [];

    /**
     * Stores temporary state for all other channels that can be accessed by
     * a Google Account.
     * 
     * Children of this array should follow the same schema as
     * $currentChannelInfo.
     */
    private array $channels = [];

    /**
     * Creates an array structure following the internal Google Account info
     * schema used by the parser.
     */
    protected static function createGoogleAccountInfo(): array
    {
        return [
            "email" => null,
            "name" => null
        ];
    }
    
    /**
     * Creates an array structure following the internal channel info schema
     * used by the parser.
     */
    protected static function createChannelInfo(): array
    {
        return [
            "name" => null,
            "photo" => null,
            "byline" => null,
            "selected" => null,
            "hasChannel" => null,
            "gaiaId" => null,
            "switchUrl" => null
        ];
    }

    public function __construct(SessionInfoBuilder $builder, object $response)
    {
        $this->builder = $builder;
        $this->response = $response;
    }

    /**
     * Parse the given account switcher response and modify this class's
     * data accordingly.
     */
    public function parse(): self
    {
        $this->retrieveGoogleAccountInfo();
        $this->retrieveChannels();

        $this->hasParsed = true;
        return $this;
    }

    // ========================================================================

    /**
     * Get the main renderer, which is the root of most other information
     * sources.
     * 
     * This is an array that will an array containing various information
     * about each of the Google Accounts.
     */
    protected function getMainRenderer(): array
    {
        return $this->response->data->actions[0]->getMultiPageMenuAction
            ->menu->multiPageMenuRenderer->sections;
    }

    /**
     * Parse the available Google Accounts in the response and set class
     * properties accordingly.
     */
    protected function retrieveAccounts(): void
    {
        $mainRenderer = $this->getMainRenderer();

        foreach ($mainRenderer as $i => $account)
        {

        }
    }

    /**
     * Get the account header renderer, which contains certain information
     * about the currently used Google Account.
     */
    protected function getAccountHeaderRenderer(object $accSection): object
    {
        return $this->getMainRenderer()->header->googleAccountHeaderRenderer;
    }

    /**
     * Get the root rendererer of all available YouTube channels on a given
     * account.
     */
    protected function getChannelItemsRenderer(object $acc): array
    {
        return $acc->contents ?? [];
    }

    /**
     * Parse the Google Account information available in the response and
     * set class properties accordingly.
     */
    protected function retrieveGoogleAccountInfo(): void
    {
        $headerInfo = $this->getAccountHeaderRenderer();

        $this->googleAccountInfo["email"] = $headerInfo->email->simpleText;
        $this->googleAccountInfo["name"] = $headerInfo->name->simpleText;
    }

    /**
     * Parse all of the channels listed in the response and set class
     * properties accordingly.
     */
    protected function retrieveChannelsForAccount(object $acc): void
    {
        $items = $this->getChannelItemsRenderer($acc);

        foreach ($items as $item)
        {
            if (isset($item->accountItem))
            {
                $channelInfo = $this->getChannelInfo($item->accountItem);

                if ($channelInfo["selected"])
                {
                    // TODO (kirasicecreamm) !!
                }

                $this->channels[] = $channelInfo;
            }
        }
    }

    /**
     * Get the information of a single channel.
     * 
     * @see getChannels()   Source of the iteration of $channel.
     */
    protected function getChannelInfo(object $channel): array
    {
        if (isset(
            $channel->serviceEndpoint->selectActiveIdentityEndpoint
                ->supportedTokens
        ))
        {
            $supportedTokens = $channel->serviceEndpoint
                ->selectActiveIdentityEndpoint->supportedTokens;

            foreach ($supportedTokens as $token)
            {
                if (isset($token->pageIdToken))
                {
                    $gaiaId = $token->pageIdToken->pageId;
                }

                if (isset($token->accountSigninToken))
                {
                    $switchUrl = $token->accountSigninToken->signinUrl;
                }
            }
        }

        $nameText = $channel->accountName->simpleText;
        $photoUrl = $channel->accountPhoto->thumbnails[0]->url;
        $bylineText = $channel->accountByline->simpleText;
        $selectedState = $channel->isSelected;
        $hasChannel = $channel->hasChannel;

        return [
            "name" => $nameText,
            "photo" => $photoUrl,
            "byline" => $bylineText,
            "selected" => $selectedState,
            "hasChannel" => $hasChannel,
            "gaiaId" => $gaiaId ?? null,
            "switchUrl" => $switchUrl ?? null
        ];
    }
}