<?php

namespace nomit\Calendar\Period;

class MonthPeriod extends AbstractPeriod implements \Iterator, \nomit\Utility\Concern\Stringable
{

    private ?PeriodInterface $currentPeriod = null;

    public static function isValid(\DateTimeInterface $dateTime): bool
    {
        return $dateTime->format('d H:i:s') === '01 00:00:00';
    }

    public static function getDateInterval(): \DateInterval
    {
        return new \DateInterval('P1M');
    }

    public function getDatePeriod(): \DatePeriod
    {
        return new \DatePeriod($this->start, new \DateInterval('P1D'), $this->end);
    }

    public function getDays(): array
    {
        $days = [];

        foreach($this->getDatePeriod() as $date) {
            $days[] = new DayPeriod($date);
        }

        return $days;
    }

    public function getFirstDayOfFirstWeek(): \DateTimeInterface
    {
        return $this->getFactory()->findFirstWeekday($this->start);
    }
    
    public function getLastDayOfLastWeek(): \DateTimeInterface
    {
        $lastDay = (clone $this->end)->sub(new \DateInterval('P1D'));
        
        return $this->getFactory()->findFirstWeekday($lastDay)->add(new \DateInterval('P6D'));
    }
    
    public function getExtendedMonth(): PeriodInterface
    {
        return $this->getFactory()->createRange($this->getFirstDayOfFirstWeek(), $this->getLastDayOfLastWeek());
    }
    
    public function current(): ?PeriodInterface
    {
        return $this->currentPeriod;
    }
    
    public function next(): void
    {
        if(!$this->valid()) {
            $this->currentPeriod = $this->getFactory()->createWeek($this->getFirstDayOfFirstWeek());
        } else {
            $this->currentPeriod = $this->currentPeriod->getNext();
            
            if($this->currentPeriod->getStart()->format('m') !== $this->start->format('m')) {
                $this->currentPeriod = null;
            }
        }
    }
    
    public function key(): int
    {
        return $this->currentPeriod->getStart()->format('W');
    }
    
    public function valid(): bool
    {
        return $this->current() !== null;
    }
    
    public function rewind(): void
    {
        $this->currentPeriod = null;
        
        $this->next();
    }

    public function toString(): string
    {
        return $this->format('F');
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}