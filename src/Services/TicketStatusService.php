<?php
namespace App\Services;

use App\Entity\GetTicketStatus;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;


class TicketStatusService {
    
    private $ticketRepository;
    private $ticketStatusRepository;
    private $tickets; 
    private $now;
    
    public function __construct (
        TicketStatusRepository $ticketStatusRepository,
        TicketRepository $ticketRepository
        ) {

        $this->daysOfDelay = 30;
        $this->ticketStatusRepository = $ticketStatusRepository;
        $this->ticketRepository = $ticketRepository;
        $this->tickets = $this->ticketRepository->findAll();
        $this->now = new \DateTime();
        $this->changeTicketStatusBasedOnDates();

    }
    
    
    private function changeTicketStatusBasedOnDates() {
        
        foreach ($this->tickets as $ticket)     {


            switch ($ticket) {

                /* from OPEN to CLOSED_INACTIVITY */
                case  $this->hasActivityStarted($ticket) && $this->isTicketStatusOpen($ticket):
                    $newStatus = GetTicketStatus::CLOSED_INACTIVITY;
                    break;
                    
                /* from PENDANT to CLOSED */
                case $this->hasActivityEndedWithDelayGap($ticket) && ($this->isTicketStatusActive($ticket) || $this->isTicketStatusPendant($ticket)):
                    $newStatus = GetTicketStatus::CLOSED;
                    break;

                /* from ACTIVE to PENDANT */
                case $this->hasActivityEnded($ticket) && $this->isTicketStatusActive($ticket):
                    $newStatus = GetTicketStatus::PENDANT;
                    break;
                
                /* from CLOSED_INACTIVITY to OPEN */
                case !$this->hasActivityStarted($ticket) && $this->isTicketStatusClosedInactivity($ticket):
                    $newStatus = GetTicketStatus::OPEN;
                    break;

                /* from CLOSED to PENDANT */
                case $this->hasActivityEnded($ticket) && !$this->hasActivityEndedWithDelayGap($ticket) && $this->isTicketStatusClosed($ticket):
                    $newStatus = GetTicketStatus::PENDANT;
                    break;

                /* from PENDANT or CLOSED to ACTIVE */
                case !$this->hasActivityEnded($ticket) && ($this->isTicketStatusPendant($ticket) || $this->isTicketStatusClosed($ticket)):
                    $newStatus = GetTicketStatus::ACTIVE;
                    break;

                default:
                    $newStatus = 0;
                    break;
            }
                
            if($newStatus > 0) {
                $newTicketStatus = $this->ticketStatusRepository->find($newStatus);
                $ticket->setTicketStatus($newTicketStatus);
                $this->ticketRepository->add($ticket, true);
            }
        }

    }
   

    private function hasActivityStarted(Ticket $ticket): bool {
        $activity = $ticket->getActivity();
        return ($activity->getStartDate() <= $this->now) ? true : false;
    }


    private function hasActivityEnded(Ticket $ticket): bool {
        $activity = $ticket->getActivity();
        return ($activity->getEndDate() <= $this->now) ? true : false;
    }


    private function hasActivityEndedWithDelayGap(Ticket $ticket): bool {
        $activity = $ticket->getActivity();
        $nowWithSubstractedDelay = new \DateTime($this->now->format('Y-m-d H:i:s'));
        $nowWithSubstractedDelay->sub(new \DateInterval('P'.$this->daysOfDelay.'D'));

        return ($activity->getEndDate() <= $nowWithSubstractedDelay) ? true : false;
    }

    private function isTicketStatusOpen(Ticket $ticket): bool {
        $ticketStatus = $ticket->getTicketStatus();
        return ($ticketStatus->getId() == GetTicketStatus::OPEN) ? true : false;
    }

    private function isTicketStatusActive(Ticket $ticket): bool {
        $ticketStatus = $ticket->getTicketStatus(); 
        return ($ticketStatus->getId() == GetTicketStatus::ACTIVE) ? true : false;
    }
    
    private function isTicketStatusPendant(Ticket $ticket): bool {
        $ticketStatus = $ticket->getTicketStatus();
        return ($ticketStatus->getId() == GetTicketStatus::PENDANT) ? true : false;
    }
    
    private function isTicketStatusClosed(Ticket $ticket): bool {
        $ticketStatus = $ticket->getTicketStatus();
        return ($ticketStatus->getId() == GetTicketStatus::CLOSED) ? true : false;
    }
    
    private function isTicketStatusClosedInactivity(Ticket $ticket): bool {
        $ticketStatus = $ticket->getTicketStatus();
        return ($ticketStatus->getId() == GetTicketStatus::CLOSED_INACTIVITY) ? true : false;
    }
      
}