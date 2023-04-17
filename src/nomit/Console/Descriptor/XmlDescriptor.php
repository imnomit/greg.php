<?php

namespace nomit\Console\Descriptor;

use nomit\Console\Command\CommandInterface;
use nomit\Console\ConsoleInterface;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Definition\DefinitionInterface;
use nomit\Console\Definition\Option\OptionInterface;

class XmlDescriptor extends AbstractDescriptor
{

    public function getDefinitionInterfaceDocument(DefinitionInterface $definition): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($definitionXML = $dom->createElement('definition'));

        $definitionXML->appendChild($argumentsXML = $dom->createElement('arguments'));
        foreach ($definition->getArguments() as $argument) {
            $this->appendDocument($argumentsXML, $this->getArgumentDocument($argument));
        }

        $definitionXML->appendChild($optionsXML = $dom->createElement('options'));
        foreach ($definition->getOptions() as $option) {
            $this->appendDocument($optionsXML, $this->getOptionDocument($option));
        }

        return $dom;
    }

    public function getCommandDocument(CommandInterface $command, bool $short = false): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($commandXML = $dom->createElement('command'));

        $commandXML->setAttribute('id', $command->getName());
        $commandXML->setAttribute('name', $command->getName());
        $commandXML->setAttribute('hidden', $command->isHidden() ? 1 : 0);

        $commandXML->appendChild($usagesXML = $dom->createElement('usages'));

        $commandXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode(str_replace("\n", "\n ", $command->getDescription())));

        if ($short) {
            foreach ($command->getAliases() as $usage) {
                $usagesXML->appendChild($dom->createElement('usage', $usage));
            }
        } else {
            $command->mergeDefinitions(false);

            foreach (array_merge([$command->getSynopsis()], $command->getAliases(), $command->getUsages()) as $usage) {
                $usagesXML->appendChild($dom->createElement('usage', $usage));
            }

            $commandXML->appendChild($helpXML = $dom->createElement('help'));
            $helpXML->appendChild($dom->createTextNode(str_replace("\n", "\n ", $command->getHelp())));

            $definitionXML = $this->getDefinitionInterfaceDocument($command->getDefinition());
            $this->appendDocument($commandXML, $definitionXML->getElementsByTagName('definition')->item(0));
        }

        return $dom;
    }

    public function getApplicationDocument(ConsoleInterface $console, string $namespace = null, bool $short = false): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($rootXml = $dom->createElement('nomit'));

        if ('UNKNOWN' !== $console->getName()) {
            $rootXml->setAttribute('name', $console->getName());
            if ('UNKNOWN' !== $console->getVersion()) {
                $rootXml->setAttribute('version', $console->getVersion());
            }
        }

        $rootXml->appendChild($commandsXML = $dom->createElement('commands'));

        $description = new ConsoleDescription($console, $namespace, true);

        if ($namespace) {
            $commandsXML->setAttribute('namespace', $namespace);
        }

        foreach ($description->getCommands() as $command) {
            $this->appendDocument($commandsXML, $this->getCommandDocument($command, $short));
        }

        if (!$namespace) {
            $rootXml->appendChild($namespacesXML = $dom->createElement('namespaces'));

            foreach ($description->getNamespaces() as $namespaceDescription) {
                $namespacesXML->appendChild($namespaceArrayXML = $dom->createElement('namespace'));
                $namespaceArrayXML->setAttribute('id', $namespaceDescription['id']);

                foreach ($namespaceDescription['commands'] as $name) {
                    $namespaceArrayXML->appendChild($commandXML = $dom->createElement('command'));
                    $commandXML->appendChild($dom->createTextNode($name));
                }
            }
        }

        return $dom;
    }

    /**
     * {@inheritdoc}
     */
    protected function describeArgument(ArgumentInterface $argument, array $options = [])
    {
        $this->writeDocument($this->getArgumentDocument($argument));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeOption(OptionInterface $option, array $options = [])
    {
        $this->writeDocument($this->getOptionDocument($option));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeDefinition(DefinitionInterface $definition, array $options = [])
    {
        $this->writeDocument($this->getDefinitionInterfaceDocument($definition));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeCommand(CommandInterface $command, array $options = [])
    {
        $this->writeDocument($this->getCommandDocument($command, $options['short'] ?? false));
    }

    /**
     * {@inheritdoc}
     */
    protected function describeConsole(ConsoleInterface $console, array $options = [])
    {
        $this->writeDocument($this->getApplicationDocument($console, $options['namespace'] ?? null, $options['short'] ?? false));
    }

    /**
     * Appends document children to parent node.
     */
    private function appendDocument(\DOMNode $parentNode, \DOMNode $importedParent)
    {
        foreach ($importedParent->childNodes as $childNode) {
            $parentNode->appendChild($parentNode->ownerDocument->importNode($childNode, true));
        }
    }

    /**
     * Writes DOM document.
     */
    private function writeDocument(\DOMDocument $dom)
    {
        $dom->formatOutput = true;
        $this->write($dom->saveXML());
    }

    private function getArgumentDocument(ArgumentInterface $argument): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->appendChild($objectXML = $dom->createElement('argument'));
        $objectXML->setAttribute('name', $argument->getName());
        $objectXML->setAttribute('is_required', $argument->isRequired() ? 1 : 0);
        $objectXML->setAttribute('is_array', $argument->isArray() ? 1 : 0);
        $objectXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode($argument->getDescription()));

        $objectXML->appendChild($defaultsXML = $dom->createElement('defaults'));
        $defaults = \is_array($argument->getDefault()) ? $argument->getDefault() : (\is_bool($argument->getDefault()) ? [var_export($argument->getDefault(), true)] : ($argument->getDefault() ? [$argument->getDefault()] : []));
        foreach ($defaults as $default) {
            $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
            $defaultXML->appendChild($dom->createTextNode($default));
        }

        return $dom;
    }

    private function getOptionDocument(OptionInterface $option): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->appendChild($objectXML = $dom->createElement('option'));
        $objectXML->setAttribute('name', '--'.$option->getName());
        $pos = strpos($option->getShortcut() ?? '', '|');
        if (false !== $pos) {
            $objectXML->setAttribute('shortcut', '-'.substr($option->getShortcut(), 0, $pos));
            $objectXML->setAttribute('shortcuts', '-'.str_replace('|', '|-', $option->getShortcut()));
        } else {
            $objectXML->setAttribute('shortcut', $option->getShortcut() ? '-'.$option->getShortcut() : '');
        }
        $objectXML->setAttribute('accept_value', $option->acceptsValue() ? 1 : 0);
        $objectXML->setAttribute('is_value_required', $option->isValueRequired() ? 1 : 0);
        $objectXML->setAttribute('is_multiple', $option->isArray() ? 1 : 0);
        $objectXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode($option->getDescription()));

        if ($option->acceptsValue()) {
            $defaults = \is_array($option->getDefault()) ? $option->getDefault() : (\is_bool($option->getDefault()) ? [var_export($option->getDefault(), true)] : ($option->getDefault() ? [$option->getDefault()] : []));
            $objectXML->appendChild($defaultsXML = $dom->createElement('defaults'));

            if (!empty($defaults)) {
                foreach ($defaults as $default) {
                    $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
                    $defaultXML->appendChild($dom->createTextNode($default));
                }
            }
        }

        if ($option->isNegatable()) {
            $dom->appendChild($objectXML = $dom->createElement('option'));
            $objectXML->setAttribute('name', '--no-'.$option->getName());
            $objectXML->setAttribute('shortcut', '');
            $objectXML->setAttribute('accept_value', 0);
            $objectXML->setAttribute('is_value_required', 0);
            $objectXML->setAttribute('is_multiple', 0);
            $objectXML->appendChild($descriptionXML = $dom->createElement('description'));
            $descriptionXML->appendChild($dom->createTextNode('Negate the "--'.$option->getName().'" option'));
        }

        return $dom;
    }

}