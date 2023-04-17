<?php

namespace nomit\Kernel\Secret;

use nomit\Utility\Concern\Stringable;
use nomit\Dependency\EnvVarLoaderInterface;
use nomit\VariableExporter\VariableExporter;
use function nomit\Dependency\Loader\Configurator\expr;

class SodiumVault extends AbstractVault implements EnvVarLoaderInterface
{

    protected string $encryption_key;

    protected string|Stringable|null $decryption_key;

    protected string $path_prefix;

    protected ?string $secrets_directory;

    public function __construct(string $secretsDirectory, string|Stringable $decryptionKey = null)
    {
        if(null !== $decryptionKey && !is_string($decryptionKey) && !(is_object($decryptionKey) && method_exists($decryptionKey, '__toString'))) {
            throw new \TypeError(sprintf('The "%s" decryption key must be a string or an object implementing "%s": instead, a "%s"-typed value was given.', __CLASS__, Stringable::class, get_debug_type($decryptionKey)));
        }

        $this->path_prefix = rtrim(strtr($secretsDirectory, '/', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($secretsDirectory) . '.';
        $this->decryption_key = $decryptionKey;
        $this->secrets_directory = $secretsDirectory;
    }

    public function generateKeys(bool $override = false): bool
    {
        $this->last_message = null;

        if(null === $this->encryption_key && '' !== $this->decryption_key = (string) $this->decryption_key) {
            $this->last_message = 'Kys cannot be generated when a decryption key has been provided during vault instantiation.';

            return false;
        }

        try {
            $this->loadKeys();
        } catch(\RuntimeException $exception) {

        }

        if('' !== $this->decryption_key && !is_file($this->path_prefix . '.encrypt.public.php')) {
            $this->export('encrypt.public', $this->encryption_key);
        }

        if(!$override && null !== $this->encryption_key) {
            $this->last_message = sprintf('Sodium keys already exist at "%s*.{public,private}" and will not be overridden.', $this->getNormalizedPath($this->path_prefix));

            return false;
        }

        $this->decryption_key = sodium_crypto_box_keypair();
        $this->encryption_key = sodium_crypto_box_publickey($this->decryption_key);

        $this->export('encrypt.public', $this->encryption_key);
        $this->export('decrypt.private', $this->decryption_key);

        $this->last_message = sprintf('A Sodium key-pair has been generated at "%s*.{public,private}.php".', $this->getNormalizedPath($this->path_prefix));

        return true;
    }

    public function seal(string $name, string $value): void
    {
        $this->last_message = null;

        $this->validateName($name);
        $this->loadKeys();

        $fileName = $this->getFileName($name);

        $this->export($fileName, sodium_crypto_box_seal($value, $this->encryption_key ?? sodium_crypto_box_publickey($this->decryption_key)));

        $list = $this->list();
        $list[$name] = null;

        uksort($list, 'strnatcmp');

        file_put_contents($this->path_prefix . '.list.php', sprintf("<?php\n\nreturn %s;\n", VariableExporter::export($list)), LOCK_EX);

        $this->last_message = sprintf('The secret named "%s" has been successfully encrypted in "%s": it may now be committed.', $name, $this->getNormalizedPath($this->path_prefix));
    }

    public function reveal(string $name): ?string
    {
        $this->last_message = null;

        $this->validateName($name);

        $fileName = $this->getFileName($name);

        if(!is_file($file = $this->path_prefix . $fileName . '.php')) {
            $this->last_message = sprintf('The secret named "%s" could not be found in "%s".', $name, $this->getNormalizedPath($this->path_prefix));

            return null;
        }

        if(!function_exists('sodium_crypto_box_seal')) {
            $this->last_message = sprintf('The secret named "%s" cannot be revealed because the Sodium PHP extension has not been installed.', $name);

            return null;
        }

        $this->loadKeys();

        if('' === $this->decryption_key) {
            $this->last_message = sprintf('The secret named "%s" cannot be revealed because no decryption key was found in "%s".', $name, $this->getNormalizedPath($this->path_prefix));

            return null;
        }

        if(false === $value = sodium_crypto_box_seal_open(include $file, $this->decryption_key)) {
            $this->last_message = sprintf('The secret named "%s" cannot be revealed because the wrong decryption key was provided for "%s".', $name, $this->getNormalizedPath($this->path_prefix));

            return null;
        }

        return $value;
    }

    public function remove(string $name): bool
    {
        $this->last_message = null;

        $this->validateName($name);

        $fileName = $this->getFileName($name);

        if(!is_file($file = $this->path_prefix . $fileName . '.php')) {
            $this->last_message = sprintf('The secret named "%s" could not be found in "%s".', $name, $this->getNormalizedPath($this->path_prefix));

            return false;
        }

        $list = $this->list();

        unset($list[$name]);

        file_put_contents($this->path_prefix . 'list.php', sprintf("<?php\n\nreturn %s;\n", VariableExporter::export($list)), LOCK_EX);

        $this->last_message = sprintf('The secret named "%s" was successfully removed from "%s".', $name, $this->getNormalizedPath($this->path_prefix));

        return @unlink($file) || !file_exists($file);
    }

    public function list(bool $reveal = false): array
    {
        $this->last_message = null;

        if(!is_file($file = $this->path_prefix . 'list.php')) {
            return[];
        }

        $secrets = include $file;

        if(!$reveal) {
            return $secrets;
        }

        foreach($secrets as $name => $value) {
            $secrets[$name]  = $this->reveal($name);
        }

        return $secrets;
    }

    protected function loadKeys(): void
    {
        if (!\function_exists('sodium_crypto_box_seal')) {
            throw new \LogicException('The "sodium" PHP extension is required to deal with secrets.');
        }

        if (null !== $this->encryption_key || '' !== $this->decryption_key = (string) $this->decryption_key) {
            return;
        }

        if (is_file($this->path_prefix . 'decrypt.private.php')) {
            $this->decryption_key = (string) include $this->path_prefix . 'decrypt.private.php';
        }

        if (is_file($this->path_prefix . 'encrypt.public.php')) {
            $this->encryption_key = (string) include $this->path_prefix . 'encrypt.public.php';
        } elseif ('' !== $this->decryption_key) {
            $this->encryption_key = sodium_crypto_box_publickey($this->decryption_key);
        } else {
            throw new \RuntimeException(sprintf('Encryption key not found in "%s".', \dirname($this->path_prefix)));
        }
    }

    protected function export(string $fileName, string $data): void
    {
        $base64 = 'decrypt.private' === $fileName ? '// nomit\_DECRYPTION_SECRET=' . base64_encode($data)."\n" : '';
        $name = basename($this->path_prefix . $fileName);
        $data = str_replace('%', '\x', rawurlencode($data));
        $data = sprintf("<?php // %s on %s\n\n%sreturn \"%s\";\n", $name, date('r'), $base64, $data);

        $this->createSecretsDirectory();

        if (false === file_put_contents($this->path_prefix . $fileName.'.php', $data, \LOCK_EX)) {
            $error = error_get_last();

            throw new \ErrorException($error['message'] ?? 'Failed to write secrets data.', 0, $error['type'] ?? \E_USER_WARNING);
        }
    }

    protected function createSecretsDirectory(): void
    {
        if($this->secrets_directory && !is_dir($this->secrets_directory) && !mkdir($this->secrets_directory, 8777, true) && !is_dir($this->secrets_directory)) {
            throw new \RuntimeException(sprintf('An error occurred while attempting to create the secrets directory "%s".', $this->secrets_directory));
        }

        $this->secrets_directory = null;
    }

    protected function getFileName(string $name): string
    {
        return $name . '.' . substr(md5($name), 0, 6);
    }

}