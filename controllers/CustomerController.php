<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/CustomerModel.php';

class CustomerController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new CustomerModel();
        $this->viewPath = 'customers';
    }

    public function index(): void {
        $this->render('index', ['customers' => $this->model->all()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'CustomerID' => $this->getInput('CustomerID'),
                'Name' => $this->getInput('name'),
                'Contact' => $this->getInput('contact'),
                'Email' => $this->getInput('email'),
                'Phone' => $this->getInput('phone'),
                'Address' => $this->getInput('address'),
                'Type' => $this->getInput('type', 'Retailer'),
            ]);
            setFlash('success', 'Customer created.');
            $this->redirect('customers');
            return;
        }
        $this->render('form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $cust = $this->model->find($id);
        if (!$cust) { setFlash('error', 'Not found.'); $this->redirect('customers'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Name' => $this->getInput('name'), 'Contact' => $this->getInput('contact'),
                'Email' => $this->getInput('email'), 'Phone' => $this->getInput('phone'),
                'Address' => $this->getInput('address'), 'Type' => $this->getInput('type'),
            ]);
            setFlash('success', 'Customer updated.');
            $this->redirect('customers');
            return;
        }
        $this->render('form', ['customer' => $cust]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Customer deleted.');
        $this->redirect('customers');
    }
}
