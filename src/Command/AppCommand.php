<?php

namespace App\Command;

use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

abstract class AppCommand extends Command
{
    protected InputInterface $input;
    protected SymfonyStyle $io;

    abstract protected function exec(): int;

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->io = new SymfonyStyle($input, $output);
        $start = new DateTimeImmutable();
        $this->syslog("Start " . $this->getName());

        try {
            $result = $this->exec();
        } catch (Throwable $throwable) {
            $this->io->error($throwable->getMessage());

            $result = self::FAILURE;
        }
        $end = new DateTimeImmutable();

        $this->syslog("Finish in " . $this->getFormattedDateInterval($end->diff($start)));

        return $result;
    }

    private function getFormattedDateInterval(DateInterval $diff): string
    {
        $format = [];

        if ($diff->y > 0) {
            $format[] = "%Y" . ($diff->y > 1 ? "years" : "year");
        }

        if ($diff->m > 0) {
            $format[] = "%M" . ($diff->m > 1 ? "months" : "month");
        }

        if ($diff->d > 0) {
            $format[] = "%D" . ($diff->d > 1 ? "days" : "day");
        }

        $format[] = "%H:%I:%S:%F";

        return $diff->format(implode(" ", $format));
    }

    protected function log(string $text, bool $withTime = false): void
    {
        if ($withTime) {
            $text = "<fg=cyan>" . (new DateTimeImmutable())->format("Y-m-d H:i:s") . "</> $text";
        }

        $this->io->writeln($text);
    }

    protected function syslog(string $text): void
    {
        $this->log("<fg=magenta>{$text}</>", true);
    }
}
