<?php

namespace nomit\Logger\Formatter;

use nomit\Logger\Logger;
use nomit\Logger\Utilities\Utilities;

class HtmlFormatter extends NormalizingFormatter
{

    /**
     * Translates Analog log levels to html color priorities.
     *
     * @var array<int, string>
     */
    protected $logLevels = [
        Logger::DEBUG     => '#CCCCCC',
        Logger::INFO      => '#28A745',
        Logger::NOTICE    => '#17A2B8',
        Logger::WARNING   => '#FFC107',
        Logger::ERROR     => '#FD7E14',
        Logger::CRITICAL  => '#DC3545',
        Logger::ALERT     => '#821722',
        Logger::EMERGENCY => '#000000',
    ];

    public function __construct(?string $dateFormat = null)
    {
        parent::__construct($dateFormat);
    }

    protected function addRow(string $th, string $td = '', bool $escapeTd = true): string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');

        if ($escapeTd) {
            $td = '<pre>'.htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8').'</pre>';
        }

        return "<tr style=\"padding: 4px;text-align: left;\">\n<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">$th:</th>\n<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">".$td."</td>\n</tr>";
    }

    protected function addTitle(string $title, int $level): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

        return '<h1 style="background: '.$this->logLevels[$level].';color: #ffffff;padding: 5px;" class="Analog-output">'.$title.'</h1>';
    }

    public function format(array $record): mixed
    {
        $output = $this->addTitle($record['level_name'], $record['level']);
        $output .= '<table cellspacing="1" width="100%" class="Analog-output">';

        $output .= $this->addRow('Message', (string) $record['message']);
        $output .= $this->addRow('Time', $this->formatDate($record['datetime']));
        $output .= $this->addRow('Channel', $record['channel']);

        if ($record['context']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';

            foreach ($record['context'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }

            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if ($record['extra']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';

            foreach ($record['extra'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }

            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }

        return $output.'</table>';
    }

    public function formatBatch(array $records): mixed
    {
        $message = '';

        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    protected function convertToString(mixed $data): string
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        $data = $this->normalize($data);

        return Utilities::jsonEncode($data, JSON_PRETTY_PRINT | Utilities::DEFAULT_JSON_FLAGS, true);
    }

}