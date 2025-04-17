<?php

// Пример массивов с историей timestamp статусов задачи и изменения сотрудников
$statusHistory = [
    ['timestamp' => 1623091200, 'status' => 'backlog'],
    ['timestamp' => 1623177600, 'status' => 'analitycs'],
    ['timestamp' => 1623264000, 'status' => 'development'],
    ['timestamp' => 1623350400, 'status' => 'testing'],
    ['timestamp' => 1623436800, 'status' => 'done']
];

$employeeHistory = [
    ['timestamp' => 1623091200, 'employee' => 'analyst_1'],
    ['timestamp' => 1623177600, 'employee' => 'analyst_2'],
    ['timestamp' => 1623264000, 'employee' => 'developer_1'],
    ['timestamp' => 1623350400, 'employee' => 'tester_1'],
    ['timestamp' => 1623436800, 'employee' => 'analyst_3']
];

// Объединяем массивы в один по времени
$combinedHistory = [];
foreach ($statusHistory as $key => $status) {
    $combinedHistory[$key] = [
        'timestamp' => $status['timestamp'],
        'status' => $status['status'],
        'employee' => $employeeHistory[$key]['employee']
    ];
}

// Выводим результат
foreach ($combinedHistory as $history) {
    echo "Timestamp: " . $history['timestamp'] . ", Status: " . $history['status'] . ", Employee: " . $history['employee'] . PHP_EOL;
}
