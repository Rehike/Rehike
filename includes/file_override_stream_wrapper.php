<?php
namespace RehikeBase;

use UnexpectedValueException;

/**
 * Overrides the handler for the file:// protocol and reimplements handlers.
 * 
 * This allows the contents of any file (but most notably PHP source code files)
 * to be overridden during runtime.
 * 
 * @see https://www.php.net/manual/en/class.streamwrapper.php Interface prototype.
 * @see https://github.com/antecedent/patchwork Project with a similar goal.
 * @see https://gist.github.com/hakre/45df61688fb3f6f10101bd2cdb24d0e6 Reference PoC.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class FileOverrideStreamWrapper
{
    protected const PROTOCOL = "file";
    
    /**
     * A resource handle to the current context, or null if no context exists.
     * 
     * This property must be public so that PHP can populate it with the actual
     * context resource.
     * 
     * @var resource
     */
    public $context;
    
    /**
     * A shared handle used by the native file API.
     * 
     * @var resource
     */
    private $handle;
    
    private static bool $isWrapped = false;
    
    /**
     * Enable the override handler.
     */
    public static function wrap(): void
    {
        $result = stream_wrapper_unregister(self::PROTOCOL);
        
        if (!$result)
        {
            throw new UnexpectedValueException("Failed to unregister override stream handler");
        }
        
        stream_wrapper_register(self::PROTOCOL, self::class);
        self::$isWrapped = true;
    }
    
    /**
     * Disable the override handler.
     */
    public static function unwrap(): void
    {
        $result = stream_wrapper_restore(self::PROTOCOL);
        
        if (!$result)
        {
            throw new UnexpectedValueException("Failed to restore original stream handler");
        }
        
        self::$isWrapped = false;
    }
    
    /**
     * Check if the override handler is wrapping file IO.
     */
    public static function getIsWrapped(): bool
    {
        return self::$isWrapped;
    }
    
    /**
     * Run a function with native IO functionality by unwrapping and then rewrapping.
     */
    public function native(callable $cb, ...$args): mixed
    {
        self::unwrap();
            
        $result = $cb(...$args);
        
        self::wrap();
        
        return $result;
    }
    
    //
    // streamHandler methods:
    //
    
    public function dir_closedir(): bool
    {
        return (bool)$this->native(fn() => closedir($this->handle));
    }
    
    public function dir_opendir(string $path, int $options): bool
    {
        $this->handle = $this->native(fn() => opendir($path, $this->context));
        return $this->handle !== false;
    }
    
    public function dir_readdir(): string|false
    {
        return $this->native(fn() => readdir($this->handle));
    }
    
    public function dir_rewinddir(): bool
    {
        return $this->native(fn() => rewinddir($this->handle));
    }
    
    public function mkdir(string $path, int $mode, int $options): bool
    {
        return $this->native(function() use ($path, $mode, $options) {
            $result = false;
            
            if (mkdir($path, $mode, (bool)($options & STREAM_MKDIR_RECURSIVE), $this->context))
            {
                $result = true;
            }
            
            return $result;
        });
    }
    
    public function rename(string $from, string $to): bool
    {
        return $this->native(fn() => rename($from, $to, $this->context));
    }
    
    public function rmdir(string $path, int $options): bool
    {
        return $this->native(fn() => rmdir($path, $this->context));
    }
    
    /**
     * @return resource
     */
    public function stream_cast(int $castAs)
    {
        return $this->handle;
    }
    
    public function stream_close(): void
    {
        $this->native(fn() => fclose($this->handle));
    }
    
    public function stream_eof(): bool
    {
        return $this->native(fn() => feof($this->handle));
    }
    
    public function stream_flush(): bool
    {
        return $this->native(fn() => fflush($this->handle));
    }
    
    public function stream_lock(int $operation): bool
    {
        if ($operation === "0" || $operation === 0)
        {
            $operation = LOCK_EX;
        }
        
        return $this->native(fn() => flock($this->handle, $operation));
    }
    
    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        return $this->native(function() use ($path, $option, $value) {
            switch ($option)
            {
                case STREAM_META_TOUCH:
                    if (empty($value))
                    {
                        return touch($path);
                    }
                    else
                    {
                        return touch($path, $value[0], $value[1]);
                    }
                case STREAM_META_OWNER_NAME:
                case STREAM_META_OWNER:
                    return chown($path, $value);
                case STREAM_META_GROUP_NAME:
                case STREAM_META_GROUP:
                    return chgrp($path, $value);
                case STREAM_META_ACCESS:
                    return chmod($path, $value);
            }
        });
    }
    
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        if (isset($this->handle))
        {
            throw new \Exception("Handle cannot be set as it already exists.");
        }
        
        $context = $this->context;
        if (null === $context)
        {
            $context = stream_context_get_default();
        }
        
        self::unwrap();
        $handle = fopen($path, $mode, true, $context);
        
        if (false === $handle)
        {
            return false;
        }
        
        $meta = stream_get_meta_data($handle);
        if (!isset($meta["uri"]))
        {
            throw new UnexpectedValueException("URI missing from stream metadata");
        }
        self::wrap();
        
        if (strstr($mode, "w") === false && strstr($path, ".php") !== false)
        {
            // temporary until this is restructured to not read that at all
            fclose($handle);
            
            self::unwrap();
            $result = file_get_contents($path, true, $context);
            self::wrap();
            
            $result = preg_replace(
                '~^(<\?php\s*)$~m',
                "\\0 declare(ticks=1);",
                $result,
                1
            );
            
            $hMemBuffer = fopen("php://memory", "rb+");
            fwrite($hMemBuffer, $result);
            rewind($hMemBuffer);
            $this->handle = $hMemBuffer;
            
            $openedPath = $meta["uri"];
            
            return true;
        }
        
        $openedPath = $meta["uri"];
        
        $this->handle = $handle;
        return true;
    }
    
    public function stream_read(int $count): string
    {
        return $this->native(fn() => fread($this->handle, $count));
    }
    
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return $this->native(fn() => fseek($this->handle, $offset, $whence));
    }
    
    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        self::unwrap();
        
        switch ($option)
        {
            case STREAM_OPTION_BLOCKING:
                $result = stream_set_blocking($this->handle, (bool)$arg1);
                break;
            case STREAM_OPTION_READ_TIMEOUT:
                $result = stream_set_timeout($this->handle, $arg1, $arg2);
                break;
            case STREAM_OPTION_WRITE_BUFFER:
                $result = stream_set_write_buffer($this->handle, $arg1);
                break;
            case STREAM_OPTION_READ_BUFFER:
                $result = stream_set_read_buffer($this->handle, $arg1);
                break;
        }
        
        self::wrap();
        
        return (bool)$result;
    }
    
    public function stream_stat(): array|false
    {
        return $this->native(fn() => fstat($this->handle));
    }
    
    public function stream_tell(): int
    {
        return $this->native(fn() => ftell($this->handle));
    }
    
    public function stream_truncate(int $newSize): bool
    {
        return $this->native(fn() => ftruncate($this->handle, $newSize));
    }
    
    public function stream_write(string $data): int
    {
        return $this->native(fn() => fwrite($this->handle, $data));
    }
    
    public function unlink(string $path): bool
    {
        return $this->native(fn() => unlink($path));
    }
    
    public function url_stat(string $path, int $flags): array|false
    {
        return $this->native(fn() => @stat($path) ?? false);
    }
}