<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\Logging\DebugLogger;

/**
 * Implements the log messages tab.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Developers
 */
class MLogTab extends MTabContent
{
    public function __construct()
    {
        $this->richDebuggerRenderer[] = new class {
            public bool $isLogTab = true;
            
            public function getLogs()
            {
                return DebugLogger::getLogs();
            }
        };
    }
}