<?php
namespace Rehike\SignInV2\Parser;

use Rehike\SignInV2\{
    Builder\SessionInfoBuilder,
    Builder\GoogleAccountInfoBuilder,
    Builder\YtChannelAccountInfoBuilder,
};

use Rehike\Util\ParsingUtils;

/**
 * Parses and retrieves information from the account switcher, requested at
 * /getAccountSwitcherEndpoint and outputs it into the session info builder.
 * 
 * @author Isabella <kawapure@gmail.com>
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

    public function __construct(SessionInfoBuilder $builder, object $response)
    {
        $this->builder = $builder;
        $this->response = $response;
    }

    /**
     * 
     */
    public function parse(): self
    {
        $accountSections = $this->getAccountSections();
        
        foreach ($accountSections as $accountSectionIndex => $accountSection)
        {
            $googleAccountBuilder = new GoogleAccountInfoBuilder($this->builder);
            
            $accountHeaderInfo = $this->retrieveGoogleAccountInfo($accountSection);
            $googleAccountBuilder->displayName = $accountHeaderInfo->displayName;
            $googleAccountBuilder->accountEmail = $accountHeaderInfo->email;
            
            // The default Google here means the active one:
            if ($accountHeaderInfo->isDefault)
            {
                $googleAccountBuilder->isActive = true;
            }
            
            foreach ($accountSection->contents as $contentSection)
            {
                if (isset($contentSection->accountItemSectionRenderer->contents[0]->accountItem))
                {
                    $channelItem = $contentSection->accountItemSectionRenderer->contents[0]->accountItem;
                    
                    // If the current channel item is the default channel for the Google
                    // account, then we can obtain a lot of information about the Google
                    // account from it:
                    if ($this->isGoogleAccountDefaultChannel($channelItem))
                    {
                        $this->addInfoToGoogleAccountBuilderFromDefaultChannel(
                            $googleAccountBuilder,
                            $channelItem
                        );
                    }
                    
                    // If we don't already know the authuser ID for the current Google
                    // account, then we'll check to see if we can obtain it from the
                    // current channel item:
                    if (!$googleAccountBuilder->authUserId)
                    {
                        if ($authUserId = $this->getAuthUserId($channelItem))
                        {
                            $googleAccountBuilder->authUserId = $authUserId;
                        }
                    }
                    
                    $channelBuilder = new YtChannelAccountInfoBuilder($googleAccountBuilder);
                    $this->addInfoToChannelBuilder($channelBuilder, $channelItem);
                }
            }
        }

        $this->hasParsed = true;
        return $this;
    }

    // ========================================================================
    
    /**
     * Helper function to find a supported token for a YouTube channel.
     */
    protected function findSupportedToken(array $supportedTokens, string $name): ?object
    {
        foreach ($supportedTokens as $token)
        {
            if (isset($token->{$name}))
            {
                return $token->{$name};
            }
        }
        
        return null;
    }

    /**
     * 
     */
    protected function getAccountSections(): array
    {
        return $this->response->data->actions[0]->getMultiPageMenuAction
            ->menu->multiPageMenuRenderer->sections;
    }

    /**
     * 
     */
    protected function getChannelItemsRenderer(object $acc): array
    {
        return $acc->contents ?? [];
    }
    
    /**
     * 
     */
    protected function retrieveGoogleAccountInfo(object $accountSection): ?AccountHeaderInfo
    {
        if (isset($accountSection->header->googleAccountHeaderRenderer))
        {
            return $this->retrieveMainGoogleAccountInfo();
        }
        else if (isset($accountSection->header->accountItemSectionHeaderRenderer))
        {
            return $this->retrieveSecondaryGoogleAccountInfo($accountSection);
        }
        
        return null;
    }
    
    /**
     * 
     */
    protected function retrieveMainGoogleAccountInfo(): AccountHeaderInfo
    {
        // The header information for the zeroth item is a googleAccountHeaderRenderer,
        // which contains information about the user's email address as well as name.
        $headerInfo = $this->getAccountSections()[0]->header->googleAccountHeaderRenderer;
        
        return new AccountHeaderInfo(
            email: $headerInfo->email->simpleText,
            displayName: $headerInfo->name->simpleText,
            isDefault: true
        );
    }
    
    protected function retrieveSecondaryGoogleAccountInfo(object $accountSection): ?AccountHeaderInfo
    {
        if (!isset($accountSection->header->accountItemSectionHeaderRenderer))
        {
            return null;
        }
        
        $headerInfo = $accountSection->header->accountItemSectionHeaderRenderer;

        $emailTitle = ParsingUtils::getText($headerInfo->title);
        
        return new AccountHeaderInfo(
            email: $emailTitle,
            displayName: null
        );
    }
    
    protected function getAuthUserId(object $accountItem): ?string
    {
        $supportedTokens = $accountItem->serviceEndpoint->selectActiveIdentityEndpoint
            ->supportedTokens;
        
        $accountSigninToken = $this->findSupportedToken($supportedTokens, "accountSigninToken");
        
        $authUserId = explode("&", explode("authuser=", $accountSigninToken->signinUrl)[1])[0];
        
        return $authUserId;
    }
    
    /**
     * 
     */
    protected function isGoogleAccountDefaultChannel(object $accountItem): bool
    {
        $supportedTokens = $accountItem->serviceEndpoint->selectActiveIdentityEndpoint
            ->supportedTokens;
        
        $accountStateToken = $this->findSupportedToken($supportedTokens, "accountStateToken");
        $datasyncIdToken = $this->findSupportedToken($supportedTokens, "datasyncIdToken");
        
        $obfuscatedGaiaId = null;
        $datasyncId = null;
        
        if ($accountStateToken)
        {
            $obfuscatedGaiaId = $accountStateToken->obfuscatedGaiaId;
        }
        
        if ($datasyncIdToken)
        {
            $datasyncId = $datasyncIdToken->datasyncIdToken;
        }
        
        if ($obfuscatedGaiaId && $datasyncId)
        {
            // Datasync IDs for the main account are equivalent to the GAIA ID
            // plus an "||" terminator. This contrasts with brand accounts,
            // which put the brand account GAIA ID first and the primary GAIA
            // ID after the "||" sequence.
            if ($obfuscatedGaiaId == ($datasyncId . "||"))
            {
                return true;
            }
        }
        
        return false;
    }
    
    protected function addInfoToGoogleAccountBuilderFromDefaultChannel(
        GoogleAccountInfoBuilder $builder,
        object $defaultChannelItem
    ): void
    {
        $supportedTokens = $defaultChannelItem->serviceEndpoint->selectActiveIdentityEndpoint
            ->supportedTokens;
        
        $accountStateToken = $this->findSupportedToken($supportedTokens, "accountStateToken");
        
        if ($accountStateToken)
        {
            $gaiaId = $accountStateToken->obfuscatedGaiaId;
            $builder->gaiaId = $gaiaId;
        }
        
        if (!$builder->displayName)
        {
            $builder->displayName = ParsingUtils::getText($defaultChannelItem->accountName);
        }
        
        if (!$builder->avatarUrl)
        {
            $builder->avatarUrl = ParsingUtils::getThumb($defaultChannelItem->accountPhoto);
        }
    }
    
    protected function addInfoToChannelBuilder(
        YtChannelAccountInfoBuilder $builder,
        object $channelItem
    ): void
    {
        $supportedTokens = $channelItem->serviceEndpoint->selectActiveIdentityEndpoint
            ->supportedTokens;
        
        $accountStateToken = $this->findSupportedToken($supportedTokens, "accountStateToken");
        
        if ($accountStateToken)
        {
            $gaiaId = $accountStateToken->obfuscatedGaiaId;
            $builder->gaiaId = $gaiaId;
        }
        
        if (!$builder->displayName)
        {
            $builder->displayName = ParsingUtils::getText($channelItem->accountName);
        }
        
        if (!$builder->avatarUrl)
        {
            $builder->avatarUrl = ParsingUtils::getThumb($channelItem->accountPhoto);
        }
        
        $builder->isActive = $channelItem->isSelected ?? false;
        
        if ($builder->isActive && !isset($this->builder->activeChannelBuilder))
        {
            $this->builder->activeChannelBuilder = $builder;
        }
    }
}