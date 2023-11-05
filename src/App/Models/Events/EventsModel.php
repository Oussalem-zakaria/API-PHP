<?php

namespace App\Models\Events;

use App\DataBase\DataBase as DB;
use PDO;

class EventsModel implements EventsInterface
{
    private $connection;

    public function __construct()
    {
        $db = new DB;
        $this->connection = $db->connect();
    }

    public function fetchAllEvents()
    {
        $req = $this->connection->prepare("SELECT * FROM event");
        $req->execute();
        $events = $req->fetchAll(PDO::FETCH_ASSOC);
        $data = $this->addCatNameToEvent($events);

        return $data;
    }

    public function getCategoryOfEvent($categorie_id)
    {
        $stm = $this->connection->prepare("SELECT name as catigorie_name from catigorie where id=:cat_id");
        $stm->bindParam(":cat_id", $categorie_id, PDO::PARAM_INT);
        $stm->execute();
        $cat = $stm->fetch(PDO::FETCH_ASSOC);

        return $cat;
    }

    public function getEventById($event_id)
    {
        $data = [];
        $stm = $this->connection->prepare("SELECT * from event where id=:event_id");
        $stm->bindParam(":event_id", $event_id, PDO::PARAM_INT);
        $stm->execute();
        $event = $stm->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            http_response_code(404);
            return $data = [
                'error' => true,
                'message' => "The event not exist"
            ];
        }

        $category = $this->getCategoryOfEvent($event['categorie_id']);
        array_push($data, array_merge($event, $category));
        return $data;
    }

    public function getEventByCategory($categorie_id)
    {
        $stm = $this->connection->prepare("SELECT * from event where categorie_id=:categorie_id");
        $stm->bindParam(":categorie_id", $categorie_id, PDO::PARAM_INT);
        $stm->execute();
        $events = $stm->fetchAll(PDO::FETCH_ASSOC);
        $data = $this->addCatNameToEvent($events);

        if (!$events) {
            http_response_code(404);
            return $data = [
                'error' => true,
                'message' => "No event found"
            ];
        }
        return $data;
    }

    function addCatNameToEvent($events)
    {
        $data = [];
        foreach ($events as $event) {
            $category = $this->getCategoryOfEvent($event['categorie_id']);
            array_push($data, array_merge($event, $category));
        }

        return $data;
    }
}
