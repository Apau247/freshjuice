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
            $id = generateId('STF');
            $this->model->create([
                'StaffID' => $id,
                'UserID' => $this->getInput('UserID') ?: null,
                'FirstName' => $this->getInput('first_name'),
                'LastName' => $this->getInput('last_name'),
                'Email' => $this->getInput('email'),
                'Phone' => $this->getInput('phone'),
                'Department' => $this->getInput('department'),
                'Position' => $this->getInput('position'),
                'DateHired' => $this->getInput('date_hired'),
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
                'FirstName' => $this->getInput('first_name'),
                'LastName' => $this->getInput('last_name'),
                'Email' => $this->getInput('email'),
                'Phone' => $this->getInput('phone'),
                'Department' => $this->getInput('department'),
                'Position' => $this->getInput('position'),
                'Status' => $this->getInput('status'),
            ]);
            setFlash('success', 'Staff updated.');
            $this->redirect('staff');
            return;
        }
        $this->render('form', ['staff' => $person]);
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
        // If route is staff/attendance/form, show the standalone form for adding
        if (($_GET['route'] ?? '') === 'staff/attendance/form') {
            $this->render('attendance_form', [
                'staffList' => $this->model->all(),
                'shifts' => $this->model->getShifts(),
                'selectedDate' => $this->getInput('date', date('Y-m-d')),
            ]);
            return;
        }
        $date = $this->getInput('date', date('Y-m-d'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('ATT');
            $this->model->getDb()->prepare(
                "INSERT INTO attendance (AttendanceID, StaffID, ShiftID, Date, ClockIn, Status) VALUES (?,?,?,?,?,?)"
            )->execute([
                $id, $this->getInput('StaffID'),
                $this->getInput('ShiftID') ?: null, $date,
                $this->getInput('ClockIn'), $this->getInput('Status', 'Present'),
            ]);
            setFlash('success', 'Attendance recorded.');
            $this->redirect('staff/attendance');
            return;
        }
        $this->render('attendance', [
            'attendanceRecords' => $this->model->getAttendance($date),
            'staffList' => $this->model->all(),
            'shifts' => $this->model->getShifts(),
            'selectedDate' => $date,
        ]);
    }

    public function createShift(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('SHF');
            $this->model->createShift([
                'ShiftID' => $id,
                'ShiftName' => $this->getInput('shift_name'),
                'StartTime' => $this->getInput('start_time'),
                'EndTime' => $this->getInput('end_time'),
                'Description' => $this->getInput('description'),
            ]);
            setFlash('success', 'Shift created.');
            $this->redirect('staff/shifts');
            return;
        }
        $this->redirect('staff/shifts');
    }

    public function editShift(): void {
        $id = $this->getInput('id');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateShift($id, [
                'ShiftName' => $this->getInput('shift_name'),
                'StartTime' => $this->getInput('start_time'),
                'EndTime' => $this->getInput('end_time'),
                'Description' => $this->getInput('description'),
            ]);
            setFlash('success', 'Shift updated.');
            $this->redirect('staff/shifts');
            return;
        }
        $shift = $this->model->findShift($id);
        if (!$shift) { setFlash('error', 'Shift not found.'); $this->redirect('staff/shifts'); return; }
        $this->render('shift_form', ['shift' => $shift]);
    }

    public function deleteShift(): void {
        $this->model->deleteShift($this->getInput('id'));
        setFlash('success', 'Shift deleted.');
        $this->redirect('staff/shifts');
    }

    public function editAttendance(): void {
        $id = $this->getInput('id');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->getDb()->prepare(
                "UPDATE attendance SET StaffID = ?, ShiftID = ?, ClockIn = ?, `Date` = ?, Status = ? WHERE AttendanceID = ?"
            )->execute([
                $this->getInput('StaffID'),
                $this->getInput('ShiftID') ?: null,
                $this->getInput('ClockIn'),
                $this->getInput('Date'),
                $this->getInput('Status', 'Present'),
                $id,
            ]);
            setFlash('success', 'Attendance updated.');
            $this->redirect('staff/attendance');
            return;
        }
        $record = $this->model->findAttendance($id);
        if (!$record) { setFlash('error', 'Attendance record not found.'); $this->redirect('staff/attendance'); return; }
        $this->render('attendance_form', [
            'record' => $record,
            'staffList' => $this->model->all(),
            'shifts' => $this->model->getShifts(),
        ]);
    }

    public function deleteAttendance(): void {
        $this->model->getDb()->prepare("DELETE FROM attendance WHERE AttendanceID = ?")->execute([$this->getInput('id')]);
        setFlash('success', 'Attendance deleted.');
        $this->redirect('staff/attendance');
    }
}
