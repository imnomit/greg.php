<?php declare(strict_types=1);

/*
 * This file is part of the Analog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Logger\Handler;

use nomit\Dumper\Dumper;

/**
 * Base Handler class providing the Handler structure, including processors and formatters
 *
 * Classes extending it should (in most cases) only implement write($record)
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @phpstan-import-type LevelName from \nomit\Logger\Logger
 * @phpstan-import-type Level from \nomit\Logger\Logger
 * @phpstan-import-type Record from \nomit\Logger\Logger
 * @phpstan-type FormattedRecord array{message: string, context: mixed[], level: Level, level_name: LevelName, channel: string, datetime: \DateTimeImmutable, extra: mixed[], formatted: mixed}
 */
abstract class AbstractProcessingHandler extends AbstractHandler implements ProcessableHandlerInterface, FormattableHandlerInterface
{
    use ProcessableHandlerTrait,
        FormattableHandlerTrait;

    /**
     * {@inheritDoc}
     */
    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        if ($this->processors) {
            $record = $this->processRecord($record);
        }

        $record['formatted'] = $this->getFormatter()->format($record);

        $this->write($record);

        return false === $this->bubble;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @phpstan-param FormattedRecord $record
     */
    abstract protected function write(array $record): void;

    /**
     * @return void
     */
    public function reset()
    {
        parent::reset();

        $this->resetProcessors();
    }
}
