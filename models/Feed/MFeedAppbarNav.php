<?php
namespace Rehike\Model\Feed;
use Rehike\Model\Appbar\MAppbarNav;
use Rehike\i18n\i18n;
use Rehike\Signin\API as SignIn;

class MFeedAppbarNav extends MAppbarNav
{
    public function __construct($feedId)
    {
        $i18n = i18n::getNamespace("appbar");

        $this->addItem($i18n->get("tabHome"), "/", $feedId == "FEwhat_to_watch" ? 2 : 0);
        $this->addItem($i18n->get("tabTrending"), "/feed/trending", $feedId == "FEtrending" ? 2 : 0);

        if (SignIn::isSignedIn()) 
            $this->addItem($i18n->get("tabSubscriptions"), "/feed/subscriptions", $feedId == "FEsubscriptions" ? 2 : 0);
    }
}