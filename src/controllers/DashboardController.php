<?php

require_once __DIR__ . '/../models/Ticket.php';

class DashboardController {
    private $ticketModel;

    public function __construct() {
        $this->ticketModel = new Ticket();
    }

    public function index() {
        $stats = $this->ticketModel->getStats();
        $ultimos_tickets = array_slice($this->ticketModel->getAllAdmin(), 0, 5); // Últimos 5 tickets

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
}