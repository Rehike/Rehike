<?php

namespace BlueLibraries\Dns\Handlers\Types;

use BlueLibraries\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\Dns\Handlers\Raw\RawDataResponse;

class TCP extends AbstractDnsHandler
{

    private int $port = 53;

    /**
     * @var mixed
     */
    private $socket = null;

    public function getType(): string
    {
        return DnsHandlerTypes::TCP;
    }

    function canUseIt(): bool
    {
        return function_exists('fsockopen');
    }

    private function getSocket()
    {

        if (!is_null($this->socket)) {
            return $this->socket;
        }

        $result = fsockopen(
            $this->nameserver,
            $this->port,
            $errorCode,
            $errorMessage,
            $this->timeout
        );

        return $this->socket = ($result === false ? null : $result);
    }

    /**
     * @param int $port
     * @return self
     */
    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @throws DnsHandlerException
     * @throws RawDataException
     */
    protected function query(string $hostName, int $typeId, int $retry = 0): ?RawDataResponse
    {

        $request = new RawDataRequest($hostName, $typeId, $this->timeout);

        $header = $request->generateHeader();
        $headerLen = strlen($header);
        $headerBinLen = $request->getBinaryHeaderLength($headerLen);

        if (!$this->write($headerBinLen)) // write the socket
        {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question length to TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_LENGTH_TO_TCP_SOCKET
            );
        }

        if (!$this->write($header, $headerLen)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question to TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_QUESTION_TO_TCP_SOCKET
            );
        }

        if (!$returnLen = $this->read(2)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to read size from TCP socket",
                DnsHandlerException::ERR_UNABLE_TO_READ_SIZE_FROM_TCP_SOCKET
            );
        }

        $returnLenData = unpack("nlength", $returnLen);
        $dataLen = $returnLenData['length'];
        $rawDataResponse = $this->read($dataLen);
        $this->close();

        if ($rawDataResponse === null) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            return null;
        }

        return new RawDataResponse($request, $rawDataResponse, $this->getType());
    }

    private function close()
    {
        if (is_null($this->socket)) {
            return;
        }
        fclose($this->socket);
        $this->socket = null;
    }

    protected function write(string $data, ?int $length = null): ?int
    {
        $result = is_null($length)
            ? fwrite($this->getSocket(), $data)
            : fwrite($this->getSocket(), $data, $length);
        return is_int($result) ? $result : null;
    }

    public function read(int $length): ?string
    {
        $result = fread($this->getSocket(), $length);
        return is_string($result) ? $result : null;
    }

    /**
     * @throws DnsHandlerException
     * @throws RawDataException
     */
    public function getDnsData(string $host, int $typeId): array
    {

        $this->validateParams($host, $typeId);
        $result = $this->query($host, $typeId);

        if (is_null($result)) {
            return [];
        }

        return $result->getData();
    }

}
