<?php

namespace BlueLibraries\Dns\Handlers\Types;

use BlueLibraries\Dns\Handlers\AbstractDnsHandler;
use BlueLibraries\Dns\Handlers\DnsHandlerException;
use BlueLibraries\Dns\Handlers\DnsHandlerTypes;
use BlueLibraries\Dns\Handlers\Raw\RawDataException;
use BlueLibraries\Dns\Handlers\Raw\RawDataRequest;
use BlueLibraries\Dns\Handlers\Raw\RawDataResponse;

class UDP extends AbstractDnsHandler
{

    private int $port = 53;

    /**
     * @var mixed
     */
    private $socket = null;

    public function getType(): string
    {
        return DnsHandlerTypes::UDP;
    }

    public function canUseIt(): bool
    {
        return function_exists('socket_create');
    }

    protected function getSocket()
    {

        if (!is_null($this->socket)) {
            return $this->socket;
        }

        $result = socket_create(
            AF_INET, SOCK_DGRAM, SOL_UDP
        );

        socket_set_option($result, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $this->timeout, 'usec' => 0));

        return $this->socket = ($result === false ? null : $result);
    }

    private function close()
    {
        $this->socket && socket_close($this->socket);
        $this->socket = null;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
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
    protected function query($hostName, $typeId, $retry = 0): ?RawDataResponse
    {
        $socket = $this->getSocket();

        if (is_null($socket)) {
            return null;
        }

        $request = new RawDataRequest($hostName, $typeId, $this->timeout);

        $header = $request->generateHeader();

        if ($socket) {
            socket_setopt($socket, SOL_SOCKET, SO_RCVBUF, 4096);
            socket_setopt($socket, SOL_SOCKET, SO_SNDBUF, 4096);
        }

        if (!$this->write($header)) {
            if ($retry < $this->retries) {
                return $this->query($hostName, $typeId, $retry + 1);
            }
            $this->close();
            throw new DnsHandlerException(
                "Failed to write question to UDP socket",
                DnsHandlerException::ERR_UNABLE_TO_WRITE_TO_UDP_SOCKET
            );
        }

        $rawDataResponse = $this->read();

        if (empty($rawDataResponse)) {
            $this->close();
            throw new DnsHandlerException(
                "Failed to read data buffer",
                DnsHandlerException::ERR_UNABLE_TO_READ_DATA_BUFFER
            );
        }

        $this->close();

        return new RawDataResponse($request, $rawDataResponse, $this->getType());
    }

    /**
     * @param string $host
     * @param int $typeId
     * @return array
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

    protected function read(): ?string
    {
        $result = socket_read($this->getSocket(), 512);
        return is_string($result) ? $result : null;
    }

    protected function write(string $header): ?int
    {
        $result = socket_sendto($this->getSocket(), $header, strlen($header), 0, $this->nameserver, $this->port);
        return is_int($result) ? $result : null;
    }

}
