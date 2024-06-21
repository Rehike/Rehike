<?php

namespace BlueLibraries\Dns\Handlers\Types;

use BlueLibraries\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Records\RecordTypes;
use BlueLibraries\Dns\Records\StringRecordUtils;
use BlueLibraries\Dns\Regex;

class Dig extends AbstractDnsHandler
{

    protected ?string $nameserver = '';

    public function getType(): string
    {
        return DnsHandlerTypes::DIG;
    }

    /**
     * @throws DnsHandlerException
     */
    public function canUseIt(): bool
    {
        $result = $this->executeCommand('dig -v 2>&1');
        return !empty($result[0]) && stripos($result[0], 'dig') === 0;
    }

    /**
     * @throws DnsHandlerException
     */
    public function getDnsData(string $host, int $typeId): array
    {
        $this->validateParams($host, $typeId);

        return StringRecordUtils::normalizeRawResult(
            $this->getDnsRawResult($host, $typeId)
        );
    }

    /**
     * @throws DnsHandlerException
     */
    public function getDnsRawResult(string $hostName, int $typeId): array
    {

        $command = $this->getCommand($hostName, $typeId);

        if (is_null($command)) {
            return [];
        }

        if (!$this->isValidCommand($command)) {
            return [];
        }

        $output = $this->executeCommand($command);

        return array_filter($output);
    }

    public function getCommand(string $hostName, int $typeId): ?string
    {
        try {
            $this->validateParams($hostName, $typeId);
        } catch (DnsHandlerException $e) {
            return null;
        }

        $recordName = RecordTypes::getName($typeId);

        $result = 'dig +nocmd +bufsize=1024 +noall +noauthority +answer +nomultiline +tries=' . ($this->retries + 1) . ' +time=' . $this->timeout;
        $result .= ' ' . $hostName . ' ' . $recordName;

        return $result . (empty($this->nameserver) ? '' : ' @' . $this->nameserver);
    }

    /**
     * @throws DnsHandlerException
     */
    protected function executeCommand(string $command): array
    {
        $result = $this->executeRawCommand($command, $output);

        if (!$this->isValidOutput($output)) {
            throw new DnsHandlerException(
                'Error: ' . json_encode($output) . PHP_EOL .
                ' Command: ' . PHP_EOL . json_encode($command),
                DnsHandlerException::ERR_UNABLE_TO_GET_RECORD
            );
        }
        return $result === false ? [] : $output;
    }

    public function isValidCommand(string $command): bool
    {
        return preg_match(Regex::DIG_COMMAND, $command) === 1;
    }

    public function isValidOutput(array $output): bool
    {
        return empty($output)
            || strpos($output[0], ';;') !== 0;
    }

    /**
     * @param string $command
     * @param $output
     * @return false|string
     */
    protected function executeRawCommand(string $command, &$output)
    {
        return exec($command, $output);
    }

}
