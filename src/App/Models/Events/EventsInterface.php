<?php
namespace App\Models\Events;

interface EventsInterface {
    public function fetchAllEvents();
    public function getEventById($event_id);
    public function getEventByCategory($category_id);
}
