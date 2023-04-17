<?php

namespace nomit\Kernel\Secret;

class EnvironmentVault extends AbstractVault
{

    protected string $dotenv_file;

    public function __construct(string $dotenvFile)
    {
        $this->dotenv_file = strtr($dotenvFile, '/', DIRECTORY_SEPARATOR);
    }

    public function generateKeys(bool $override = false): bool
    {
        $this->last_message = sprintf('The "%s" vault does not encrypt secrets, and so does not need keys.', self::class);

        return false;
    }

    public function seal(string $name, string $value): void
    {
        $this->last_message = null;

        $this->validateName($name);

        $value = str_replace("'", "'\\''", $value);

        $content = is_file($this->dotenv_file) ? file_get_contents($this->dotenv_file) : '';
        $content = preg_replace("/^$name=((\\\\'|'[^']++')++|.*)/m", "$name='$value'", $content, -1, $count);

        if(!$count) {
            $content .= "$name='$value'\n";
        }

        file_put_contents($this->dotenv_file, $content, FILE_APPEND);

        $this->last_message = sprintf('The secret "%s" has been successfully %s in "%s".', $name, $count ? 'added' : 'updated', $this->getNormalizedPath($this->dotenv_file));
    }

    public function reveal(string $name): ?string
    {
        $this->last_message = null;

        $this->validateName($name);

        $value = is_string($_SERVER[$name] ?? null) && !str_starts_with($name, 'HTTP_') ? $_SERVER[$name] : ($_ENV[$name] ?? null);

        if(null === $value) {
            $this->last_message = sprintf('The secret named "%s" could not be found in "%s".', $name, $this->getNormalizedPath($this->dotenv_file));

            return null;
        }

        return $value;
    }

    public function remove(string $name): bool
    {
        $this->last_message = null;

        $this->validateName($name);

        $content = is_file($this->dotenv_file) ? file_get_contents($this->dotenv_file) : '';
        $content = preg_replace("/^$name=((\\\\'|'[^']++')++|.*)\n?/m", '', $content, -1, $count);

        if($count) {
            file_put_contents($this->dotenv_file, $content);

            $this->last_message = sprintf('The secret named "%s" has been successfully removed from "%s".', $name, $this->getNormalizedPath($this->dotenv_file));

            return true;
        }

        $this->last_message = sprintf('The secret named "%s" could not be found in "%s".', $name, $this->getNormalizedPath($this->dotenv_file));

        return false;
    }

    public function list(bool $reveal = false): array
    {
        $this->last_message = null;

        $secrets = [];

        foreach($_ENV as $key => $value) {
            if(preg_match('/^\w+$/', $key)) {
                $secrets[$key] = $reveal ? $value : null;
            }
        }

        foreach($_SERVER as $key => $value) {
            if(is_string($value) && preg_match('/^\w+$/D', $key)) {
                $secrets[$key] = $reveal ? $value : null;
            }
        }

        return $secrets;
    }


}