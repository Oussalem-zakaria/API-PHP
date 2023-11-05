<?php

namespace App\Controllers\Events;

use App\Models\Events\EventsInterface as EventsInterface;

class EventsController
{
    private $event;

    public function __construct(EventsInterface $eventModel)
    {
        $this->event = $eventModel;
    }

    public function index()
    {
        $events = $this->event->fetchAllEvents();

        echo json_encode([
            'events' => $events
        ]);
    }

    public function eventById($event_id)
    {
        $event_id = filter_var($event_id, FILTER_VALIDATE_INT);
        if (!$event_id) {
            echo json_encode([
                'error' => true,
                'message' => 'The event not exist'
            ]);
        } else {
            $event = $this->event->getEventById($event_id);
            echo json_encode([
                'events' => $event
            ]);
        }
    }

    public function eventByCategory($category_id)
    {
        $category_id = filter_var($category_id, FILTER_VALIDATE_INT);
        if (!$category_id) {
            echo json_encode([
                'error' => true,
                'message' => 'The event not exist'
            ]);
        } else {
            $event = $this->event->getEventByCategory($category_id);
            echo json_encode([
                'events' => $event
            ]);
        }
    }
}
