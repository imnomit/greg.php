<?php

namespace nomit\Kernel\Command;

use nomit\Console\Command\Command;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Kernel\Secret\VaultInterface;

final class EncryptSecretsFromLocalCommand extends Command
{

    protected VaultInterface $vault;

    protected ?VaultInterface $local_vault;

    public function __construct(VaultInterface $vault, VaultInterface $localVault = null)
    {
        parent::__construct('secrets:encrypt-from-local');

        $this->vault = $vault;
        $this->local_vault = $localVault;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Encrypts all local secrets to the vault')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command encrypts all locally overridden secrets to the vault.
    <info>%command.full_name%</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ConsoleStyle($input, $output);

        if (null === $this->local_vault) {
            $io->error('The local vault is disabled.');

            return 1;
        }

        foreach ($this->vault->list(true) as $name => $value) {
            $localValue = $this->local_vault->reveal($name);

            if (null !== $localValue && $value !== $localValue) {
                $this->vault->seal($name, $localValue);
            } elseif (null !== $message = $this->local_vault->getLastMessage()) {
                $io->error($message);

                return 1;
            }
        }

        return 0;
    }

}