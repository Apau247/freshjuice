<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/StaffModel.php';

class StaffController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new StaffModel();
        $this->viewPath = 'staff';
    }

    public function index(): void {
        $this->render('index', ['staff' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'StaffID' => $this->getInput('StaffID'),
                'UserID' => $this->getInput('UserID') ?: null,
                'FirstName' => $this->getInput('FirstName'),
                'LastName' => $this->getInput('LastName'),
                'Email' => $this->getInput('Email'),
                'Phone' => $this->getInput('Phone'),
                'Department' => $this->getInput('Department'),
                'Position' => $this->getInput('Position'),
                'DateHired' => $this->getInput('DateHired'),
            ]);
            setFlash('success', 'Staff created.');
            $this->redirect('staff');
            return;
        }
        $this->render('form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $person = $this->model->find($id);
        if (!$person) { setFlash('error', 'Not found.'); $this->redirect('staff'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'FirstName' => $this->getInput('FirstName'),
                'LastName' => $this->getInput('LastName'),
                'Email' => $this->getInput('Email'),
                'Phone' => $this->getInput('Phone'),
                'Department' => $this->getInput('Department'),
                'Position' => $this->getInput('Position'),
                'Status' => $this->getInput('Status'),
            ]);
            setFlash('success', 'Staff updated.');
            $this->redirect('staff');
            return;
        }
        $this->render('form', ['person' => $person]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Staff deleted.');
        $this->redirect('staff');
    }

    public function shifts(): void {
        $this->render('shifts', ['shifts' => $this->model->getShifts()]);
    }

    public function attendance(): void {
        $date = $this->getInput('date', date('Y-m-d'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->db->prepare(
                "INSERT INTO attendance (AttendanceID, StaffID, ShiftID, Date, ClockIn, Status) VALUES (?,?,?,?,?,?)"
            )->execute([
                $this->getInput('AttendanceID'), $this->getInput('StaffID'),
                $this->getInput('ShiftID') ?: null, $date,
                $this->getInput('ClockIn'), $this->getInput('Status', 'Present'),
            ]);
            setFlash('success', 'Attendance recorded.');
            $this->redirect('staff/attendance');
            return;
        }
        $this->render('attendance', [
            'attendance' => $this->model->getAttendance($date),
            'staffList' => $this->model->all(),
            'shifts' => $this->model->getShifts(),
            'selectedDate' => $date,
        ]);
    }
}
