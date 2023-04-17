<?php

namespace nomit\FileSystem;

use FilesystemIterator;
use nomit\Dumper\Dumper;
use nomit\FileSystem\Exception\LockException;
use nomit\FileSystem\Locking\Lock;
use nomit\FileSystem\Locking\LockInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use nomit\FileSystem\Exception\FileNotFoundException;
use nomit\FileSystem\Exception\InvalidArgumentException;
use nomit\FileSystem\Exception\IOException;
use Throwable;
use Traversable;
use TypeError;
use function array_slice;
use function count;
use function dirname;
use function func_num_args;
use function function_exists;
use function is_array;
use function is_int;
use function strlen;
use const DIRECTORY_SEPARATOR;
use const FILE_APPEND;
use const PHP_MAXPATHLEN;
use const PHP_URL_HOST;
use const PHP_URL_SCHEME;
use const PHP_VERSION_ID;

class FileSystem
{

    /**
     * @var string|null
     */
    protected static ?string $lastError = null;

    private ?LockInterface $lock = null;

    /**
     * @param string $pathName
     * @return bool
     */
    public static function isFile(string $pathName): bool
    {
        return is_file($pathName) && is_readable($pathName);
    }

    /**
     * @param string $pathName
     * @return bool
     */
    public static function isDirectory(string $pathName): bool
    {
        return is_dir($pathName) && is_readable($pathName);
    }

    /**
     * @internal
     */
    public static function handleError($type, $msg)
    {
        self::$lastError = $msg;
    }

    public function lock(string $file): void
    {
        if($this->lock && $this->lock->getPathname() === $file) {
            return;
        }

        $this->lock = new Lock($file);
        $this->lock->lock();
    }

    public function isLocked(bool $release = false): bool
    {
        return $this->lock?->isLocked($release);
    }

    public function unlock(): void
    {
        $this->lock?->unlock();
    }

    /**
     * @param string $file
     * @param string $contents
     * @param bool $append
     */
    public function write(string $file, string $contents, bool $append = false)
    {
        if (($success = @file_put_contents($file, $contents, ($append ? FILE_APPEND : 0))) === false) {
            throw new FileNotFoundException(sprintf('Failed to write to "%s" because an error occurred.', $file), 0, null, $file);
        }

        return $success;
    }

    /**
     * @param string $file
     * @return false|string
     */
    public function read(string $file)
    {
        if (!$this->exists($file)) {
            throw new FileNotFoundException($file);
        }

        return file_get_contents($file);
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files)
    {
        $maxPathLength = PHP_MAXPATHLEN - 2;

        foreach ($this->toIterable($files) as $file) {
            if (strlen($file) > $maxPathLength) {
                throw new IOException(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength), 0, null, $file);
            }

            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets access and modification time of file.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to create
     * @param int|null $time The touch time as a Unix timestamp, if not supplied the current system time is used
     * @param int|null $atime The access time as a Unix timestamp, if not supplied the current system time is used
     *
     * @throws IOException When touch fails
     */
    public function touch($files, int $time = null, int $atime = null)
    {
        foreach ($this->toIterable($files) as $file) {
            if (!($time ? self::box('touch', $file, $time, $atime) : self::box('touch', $file))) {
                throw new IOException(sprintf('Failed to touch "%s": ', $file) . self::$lastError, 0, null, $file);
            }
        }
    }

    /**
     * Change mode for an array of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to change mode
     * @param int $mode The new mode (octal)
     * @param int $umask The mode mask (octal)
     * @param bool $recursive Whether change the mod recursively or not
     *
     * @throws IOException When the change fails
     */
    public function changeMode($files, int $mode, int $umask = 0000, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if ((PHP_VERSION_ID < 80000 || is_int($mode)) && !self::box('chmod', $file, $mode & ~$umask)) {
                throw new IOException(sprintf('Failed to chmod file "%s": ', $file) . self::$lastError, 0, null, $file);
            }
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->changeMode(new FilesystemIterator($file), $mode, $umask, true);
            }
        }
    }

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to change owner
     * @param string|int $user A user name or number
     * @param bool $recursive Whether change the owner recursively or not
     *
     * @throws IOException When the change fails
     */
    public function changeOwnership($files, $user, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->changeOwnership(new FilesystemIterator($file), $user, true);
            }
            if (is_link($file) && function_exists('lchown')) {
                if (!self::box('lchown', $file, $user)) {
                    throw new IOException(sprintf('Failed to chown file "%s": ', $file) . self::$lastError, 0, null, $file);
                }
            } else {
                if (!self::box('chown', $file, $user)) {
                    throw new IOException(sprintf('Failed to chown file "%s": ', $file) . self::$lastError, 0, null, $file);
                }
            }
        }
    }

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to change group
     * @param string|int $group A group name or number
     * @param bool $recursive Whether change the group recursively or not
     *
     * @throws IOException When the change fails
     */
    public function changeGroup($files, $group, bool $recursive = false)
    {
        foreach ($this->toIterable($files) as $file) {
            if ($recursive && is_dir($file) && !is_link($file)) {
                $this->changeGroup(new FilesystemIterator($file), $group, true);
            }
            if (is_link($file) && function_exists('lchgrp')) {
                if (!self::box('lchgrp', $file, $group)) {
                    throw new IOException(sprintf('Failed to chgrp file "%s": ', $file) . self::$lastError, 0, null, $file);
                }
            } else {
                if (!self::box('chgrp', $file, $group)) {
                    throw new IOException(sprintf('Failed to chgrp file "%s": ', $file) . self::$lastError, 0, null, $file);
                }
            }
        }
    }

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @throws IOException When symlink fails
     */
    public function symbolicLink(string $originDir, string $targetDir, bool $copyOnWindows = false)
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $originDir = strtr($originDir, '/', '\\');
            $targetDir = strtr($targetDir, '/', '\\');

            if ($copyOnWindows) {
                $this->mirror($originDir, $targetDir);

                return;
            }
        }

        $this->makeDirectory(dirname($targetDir));

        if (is_link($targetDir)) {
            if (readlink($targetDir) === $originDir) {
                return;
            }
            $this->remove($targetDir);
        }

        if (!self::box('symlink', $originDir, $targetDir)) {
            $this->linkException($originDir, $targetDir, 'symbolic');
        }
    }

    /**
     * Mirrors a directory to another.
     *
     * Copies files and directories from the origin directory into the target directory. By default:
     *
     *  - existing files in the target directory will be overwritten, except if they are newer (see the `override` option)
     *  - files in the target directory that do not exist in the source directory will not be deleted (see the `delete` option)
     *
     * @param Traversable|null $iterator Iterator that filters which files and directories to copy, if null a recursive iterator is created
     * @param array $options An array of boolean options
     *                                    Valid options are:
     *                                    - $options['override'] If true, target files newer than origin files are overwritten (see copy(), defaults to false)
     *                                    - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink(), defaults to false)
     *                                    - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */
    public function mirror(string $originDir, string $targetDir, Traversable $iterator = null, array $options = [])
    {
        $targetDir = rtrim($targetDir, '/\\');
        $originDir = rtrim($originDir, '/\\');
        $originDirLen = strlen($originDir);

        if (!$this->exists($originDir)) {
            throw new IOException(sprintf('The origin directory specified "%s" was not found.', $originDir), 0, null, $originDir);
        }

        // Iterate in destination folder to remove obsolete entries
        if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
            $deleteIterator = $iterator;
            if (null === $deleteIterator) {
                $flags = FilesystemIterator::SKIP_DOTS;
                $deleteIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($targetDir, $flags), RecursiveIteratorIterator::CHILD_FIRST);
            }
            $targetDirLen = strlen($targetDir);
            foreach ($deleteIterator as $file) {
                $origin = $originDir . substr($file->getPathname(), $targetDirLen);
                if (!$this->exists($origin)) {
                    $this->remove($file);
                }
            }
        }

        $copyOnWindows = $options['copy_on_windows'] ?? false;

        if (null === $iterator) {
            $flags = $copyOnWindows ? FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS : FilesystemIterator::SKIP_DOTS;
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($originDir, $flags), RecursiveIteratorIterator::SELF_FIRST);
        }

        $this->makeDirectory($targetDir);
        $filesCreatedWhileMirroring = [];

        foreach ($iterator as $file) {
            if ($file->getPathname() === $targetDir || $file->getRealPath() === $targetDir || isset($filesCreatedWhileMirroring[$file->getRealPath()])) {
                continue;
            }

            $target = $targetDir . substr($file->getPathname(), $originDirLen);
            $filesCreatedWhileMirroring[$target] = true;

            if (!$copyOnWindows && is_link($file)) {
                $this->symbolicLink($file->getLinkTarget(), $target);
            } elseif (is_dir($file)) {
                $this->makeDirectory($target);
            } elseif (is_file($file)) {
                $this->copy($file, $target, $options['override'] ?? false);
            } else {
                throw new IOException(sprintf('Unable to guess "%s" file type.', $file), 0, null, $file);
            }
        }
    }

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public function remove($files, bool $isRecursive = false)
    {
        if ($files instanceof Traversable) {
            $files = iterator_to_array($files, false);
        } elseif (!is_array($files)) {
            $files = [$files];
        }

        self::doRemove($files, $isRecursive);
    }

    private static function doRemove(array $files, bool $isRecursive): void
    {
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (is_link($file)) {
                // See https://bugs.php.net/52176
                if (!(self::box('unlink', $file) || '\\' !== DIRECTORY_SEPARATOR || self::box('rmdir', $file)) && file_exists($file)) {
                    throw new IOException(sprintf('Failed to remove symlink "%s": ', $file) . self::$lastError);
                }
            } elseif (is_dir($file)) {
                if (!$isRecursive) {
                    $tmpName = dirname(realpath($file)) . '/.' . strrev(strtr(base64_encode(random_bytes(2)), '/=', '-.'));

                    if (file_exists($tmpName)) {
                        try {
                            self::doRemove([$tmpName], true);

                            return;
                        } catch (IOException $e) {
                        }
                    }

                    if (!file_exists($tmpName) && self::box('rename', $file, $tmpName)) {
                        $origFile = $file;
                        $file = $tmpName;
                    } else {
                        $origFile = null;
                    }
                }

                if(!is_readable($file)) {
                    return;
                }

                $files = new FilesystemIterator($file, FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS);

                self::doRemove(iterator_to_array($files, true), true);

                if (!self::box('rmdir', $file) && file_exists($file) && !$isRecursive) {
                    $lastError = self::$lastError;

                    if (null !== $origFile && self::box('rename', $file, $origFile)) {
                        $file = $origFile;
                    }

                    throw new IOException(sprintf('Failed to remove directory "%s": ', $file) . $lastError);
                }
            } elseif (!self::box('unlink', $file) && (false !== strpos(self::$lastError, 'Permission denied') || file_exists($file))) {
                throw new IOException(sprintf('Failed to remove file "%s": ', $file) . self::$lastError);
            }
        }
    }

    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
    {
        $originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
        if ($originIsLocal && !is_file($originFile)) {
            throw new FileNotFoundException(sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null, $originFile);
        }

        $this->makeDirectory(dirname($targetFile));

        $doCopy = true;
        if (!$overwriteNewerFiles && null === parse_url($originFile, PHP_URL_HOST) && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        }

        if ($doCopy) {
            // https://bugs.php.net/64634
            if (!$source = self::box('fopen', $originFile, 'r')) {
                throw new IOException(sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading: ', $originFile, $targetFile) . self::$lastError, 0, null, $originFile);
            }

            // Stream context created to allow files overwrite when using FTP stream wrapper - disabled by default
            if (!$target = self::box('fopen', $targetFile, 'w', false, stream_context_create(['ftp' => ['overwrite' => true]]))) {
                throw new IOException(sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing: ', $originFile, $targetFile) . self::$lastError, 0, null, $originFile);
            }

            $bytesCopied = stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);

            if (!is_file($targetFile)) {
                throw new IOException(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null, $originFile);
            }

            if ($originIsLocal) {
                // Like `cp`, preserve executable permission bits
                self::box('chmod', $targetFile, fileperms($targetFile) | (fileperms($originFile) & 0111));

                if ($bytesCopied !== $bytesOrigin = filesize($originFile)) {
                    throw new IOException(sprintf('Failed to copy the whole content of "%s" to "%s" (%g of %g bytes copied).', $originFile, $targetFile, $bytesCopied, $bytesOrigin), 0, null, $originFile);
                }
            }
        }
    }

    /**
     * Creates a directory recursively.
     *
     * @param string|iterable $dirs The directory path
     *
     * @throws IOException On any directory creation failure
     */
    public function makeDirectory($dirs, int $mode = 0777, bool $throwException = true)
    {
        foreach ($this->toIterable($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!self::box('mkdir', $dir, $mode, true) && !is_dir($dir)) {
                if(!$throwException) {
                    return false;
                }

                throw new IOException(sprintf('Failed to create "%s": ', $dir) . self::$lastError, 0, null, $dir);
            }
        }

        return true;
    }

    /**
     * @param $files
     * @return iterable
     */
    private function toIterable($files): iterable
    {
        return is_array($files) || $files instanceof Traversable ? $files : [$files];
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    private static function box(callable $func, ...$args)
    {
        self::$lastError = null;
        set_error_handler(__CLASS__ . '::handleError');
        try {
            $result = $func(...$args);
            restore_error_handler();

            return $result;
        } catch (Throwable $e) {
        }
        restore_error_handler();

        throw $e;
    }

    /**
     * @param string $linkType Name of the link type, typically 'symbolic' or 'hard'
     */
    private function linkException(string $origin, string $target, string $linkType)
    {
        if (self::$lastError) {
            if ('\\' === DIRECTORY_SEPARATOR && false !== strpos(self::$lastError, 'error code(1314)')) {
                throw new IOException(sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType), 0, null, $target);
            }
        }
        throw new IOException(sprintf('Failed to create "%s" link from "%s" to "%s": ', $linkType, $origin, $target) . self::$lastError, 0, null, $target);
    }

    /**
     * Creates a hard link, or several hard links to a file.
     *
     * @param string|string[] $targetFiles The target file(s)
     *
     * @throws FileNotFoundException When original file is missing or not a file
     * @throws IOException           When link fails, including if link already exists
     */
    public function hardlink(string $originFile, $targetFiles)
    {
        if (!$this->exists($originFile)) {
            throw new FileNotFoundException(null, 0, null, $originFile);
        }

        if (!is_file($originFile)) {
            throw new FileNotFoundException(sprintf('Origin file "%s" is not a file.', $originFile));
        }

        foreach ($this->toIterable($targetFiles) as $targetFile) {
            if (is_file($targetFile)) {
                if (fileinode($originFile) === fileinode($targetFile)) {
                    continue;
                }
                $this->remove($targetFile);
            }

            if (!self::box('link', $originFile, $targetFile)) {
                $this->linkException($originFile, $targetFile, 'hard');
            }
        }
    }

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     *
     * @return string|null
     */
    public function readlink(string $path, bool $canonicalize = false)
    {
        if (!$canonicalize && !is_link($path)) {
            return null;
        }

        if ($canonicalize) {
            if (!$this->exists($path)) {
                return null;
            }

            if ('\\' === DIRECTORY_SEPARATOR && PHP_VERSION_ID < 70410) {
                $path = readlink($path);
            }

            return realpath($path);
        }

        if ('\\' === DIRECTORY_SEPARATOR && PHP_VERSION_ID < 70400) {
            return realpath($path);
        }

        return readlink($path);
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative(string $endPath, string $startPath)
    {
        if (!$this->isAbsolutePath($startPath)) {
            throw new InvalidArgumentException(sprintf('The start path "%s" is not absolute.', $startPath));
        }

        if (!$this->isAbsolutePath($endPath)) {
            throw new InvalidArgumentException(sprintf('The end path "%s" is not absolute.', $endPath));
        }

        // Normalize separators on Windows
        if ('\\' === DIRECTORY_SEPARATOR) {
            $endPath = str_replace('\\', '/', $endPath);
            $startPath = str_replace('\\', '/', $startPath);
        }

        $splitDriveLetter = function ($path) {
            return (strlen($path) > 2 && ':' === $path[1] && '/' === $path[2] && ctype_alpha($path[0]))
                ? [substr($path, 2), strtoupper($path[0])]
                : [$path, null];
        };

        $splitPath = function ($path) {
            $result = [];

            foreach (explode('/', trim($path, '/')) as $segment) {
                if ('..' === $segment) {
                    array_pop($result);
                } elseif ('.' !== $segment && '' !== $segment) {
                    $result[] = $segment;
                }
            }

            return $result;
        };

        [$endPath, $endDriveLetter] = $splitDriveLetter($endPath);
        [$startPath, $startDriveLetter] = $splitDriveLetter($startPath);

        $startPathArr = $splitPath($startPath);
        $endPathArr = $splitPath($endPath);

        if ($endDriveLetter && $startDriveLetter && $endDriveLetter != $startDriveLetter) {
            // End path is on another drive, so no relative path exists
            return $endDriveLetter . ':/' . ($endPathArr ? implode('/', $endPathArr) . '/' : '');
        }

        // Find for which directory the common path stops
        $index = 0;
        while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
            ++$index;
        }

        // Determine how deep the start path is relative to the common path (ie, "web/bundles" = 2 levels)
        if (1 === count($startPathArr) && '' === $startPathArr[0]) {
            $depth = 0;
        } else {
            $depth = count($startPathArr) - $index;
        }

        // Repeated "../" for each level need to reach the common path
        $traverser = str_repeat('../', $depth);

        $endPathRemainder = implode('/', array_slice($endPathArr, $index));

        // Construct $endPath from traversing to the common path, then to the remaining $endPath
        $relativePath = $traverser . ('' !== $endPathRemainder ? $endPathRemainder . '/' : '');

        return '' === $relativePath ? './' : $relativePath;
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @return bool
     */
    public function isAbsolutePath(string $file)
    {
        return '' !== $file && (strspn($file, '/\\', 0, 1)
                || (strlen($file) > 3 && ctype_alpha($file[0])
                    && ':' === $file[1]
                    && strspn($file, '/\\', 2, 1)
                )
                || null !== parse_url($file, PHP_URL_SCHEME)
            );
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string|resource $content The data to write into the file
     *
     * @throws IOException if the file cannot be written to
     */
    public function dumpFile(string $filename, $content)
    {
        if (is_array($content)) {
            throw new TypeError(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.', __METHOD__));
        }

        $dir = dirname($filename);

        if (!is_dir($dir)) {
            $this->makeDirectory($dir);
        }

        if (!is_writable($dir)) {
            throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
        }

        // Will create a temp file with 0600 access rights
        // when the filesystem supports chmod.
        $tmpFile = $this->temporaryName($dir, basename($filename));

        try {
            if (false === self::box('file_put_contents', $tmpFile, $content)) {
                throw new IOException(sprintf('Failed to write file "%s": ', $filename) . self::$lastError, 0, null, $filename);
            }

            self::box('chmod', $tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());

            $this->rename($tmpFile, $filename, true);
        } finally {
            if (file_exists($tmpFile)) {
                self::box('unlink', $tmpFile);
            }
        }
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     * @param string $suffix The suffix of the generated temporary filename
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function temporaryName(string $dir, string $prefix/*, string $suffix = ''*/)
    {
        $suffix = func_num_args() > 2 ? func_get_arg(2) : '';
        [$scheme, $hierarchy] = $this->getSchemeAndHierarchy($dir);

        // If no scheme or scheme is "file" or "gs" (Google Cloud) create temp file in local filesystem
        if ((null === $scheme || 'file' === $scheme || 'gs' === $scheme) && '' === $suffix) {
            // If tempnam failed or no scheme return the filename otherwise prepend the scheme
            if ($tmpFile = self::box('tempnam', $hierarchy, $prefix)) {
                if (null !== $scheme && 'gs' !== $scheme) {
                    return $scheme . '://' . $tmpFile;
                }

                return $tmpFile;
            }

            throw new IOException('A temporary file could not be created: ' . self::$lastError);
        }

        // Loop until we create a valid temp file or have reached 10 attempts
        for ($i = 0; $i < 10; ++$i) {
            // Create a unique filename
            $tmpFile = $dir . '/' . $prefix . uniqid(mt_rand(), true) . $suffix;

            // Use fopen instead of file_exists as some streams do not support stat
            // Use mode 'x+' to atomically check existence and create to avoid a TOCTOU vulnerability
            if (!$handle = self::box('fopen', $tmpFile, 'x+')) {
                continue;
            }

            // Close the file if it was successfully opened
            self::box('fclose', $handle);

            return $tmpFile;
        }

        throw new IOException('A temporary file could not be created: ' . self::$lastError);
    }

    /**
     * Gets a 2-tuple of scheme (may be null) and hierarchical part of a filename (e.g. file:///tmp -> [file, tmp]).
     */
    private function getSchemeAndHierarchy(string $filename): array
    {
        $components = explode('://', $filename, 2);

        return 2 === count($components) ? [$components[0], $components[1]] : [null, $components[0]];
    }

    /**
     * Renames a file or a directory.
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = false)
    {
        // we check that target does not exist
        if (!$overwrite && $this->isReadable($target)) {
            throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
        }

        if (!self::box('rename', $origin, $target)) {
            if (is_dir($origin)) {
                // See https://bugs.php.net/54097 & https://php.net/rename#113943
                $this->mirror($origin, $target, null, ['override' => $overwrite, 'delete' => $overwrite]);
                $this->remove($origin);

                return true;
            }
            throw new IOException(sprintf('Cannot rename "%s" to "%s": ', $origin, $target) . self::$lastError, 0, null, $target);
        }

        return true;
    }

    /**
     * Tells whether a file exists and is readable.
     *
     * @throws IOException When windows path is longer than 258 characters
     */
    private function isReadable(string $filename): bool
    {
        $maxPathLength = PHP_MAXPATHLEN - 2;

        if (strlen($filename) > $maxPathLength) {
            throw new IOException(sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength), 0, null, $filename);
        }

        return is_readable($filename);
    }

    /**
     * Appends content to an existing file.
     *
     * @param string|resource $content The content to append
     *
     * @throws IOException If the file is not writable
     */
    public function appendToFile(string $filename, $content)
    {
        if (is_array($content)) {
            throw new TypeError(sprintf('Argument 2 passed to "%s()" must be string or resource, array given.', __METHOD__));
        }

        $dir = dirname($filename);

        if (!is_dir($dir)) {
            $this->makeDirectory($dir);
        }

        if (!is_writable($dir)) {
            throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
        }

        if (false === self::box('file_put_contents', $filename, $content, FILE_APPEND)) {
            throw new IOException(sprintf('Failed to write file "%s": ', $filename) . self::$lastError, 0, null, $filename);
        }
    }

    /**
     * @param string $file
     * @return array
     */
    public function metadata(string $file): array
    {
        if (!$this->exists($file)) {
            throw new FileNotFoundException($file);
        }

        return stat($file);
    }

    /**
     * @param string $path
     * @param bool $removeParentToo
     */
    public function empty(string $path, bool $removeParentToo = false): void
    {
        if ($path[strlen($path) - 1] !== '/') {
            $path .= '/';
        }

        $children = glob($path . '*', GLOB_MARK);

        foreach ($children as $child_path) {
            if (is_dir($child_path)) {
                $this->empty($child_path, true);

                continue;
            }

            if (!@unlink($child_path)) {
                throw new IOException(sprintf('An error occurred while attempting to remove the child element "%s".', $child_path));
            }
        }

        if ($removeParentToo) {
            $this->remove($path);
        }
    }

}