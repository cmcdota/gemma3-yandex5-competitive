<?php

// Пример массивов с историей timestamp статусов задачи и изменения сотрудников
$statusHistory = [
    ['timestamp' => '2023-01-01T10:00:00', 'status' => 'backlog'],
    ['timestamp' => '2023-01-02T12:00:00', 'status' => 'analitycs'],
    ['timestamp' => '2023-01-03T14:00:00', 'status' => 'development'],
    ['timestamp' => '2023-01-04T16:00:00', 'status' => 'testing'],
    ['timestamp' => '2023-01-05T18:00:00', 'status' => 'done']
];

$employeeHistory = [
    ['timestamp' => '2023-01-01T10:00:00', 'employee' => 'сотрудник 1'],
    ['timestamp' => '2023-01-02T12:00:00', 'employee' => 'сотрудник 2'],
    ['timestamp' => '2023-01-03T14:00:00', 'employee' => 'сотрудник 3'],
    ['timestamp' => '2023-01-04T16:00:00', 'employee' => 'сотрудник 4']
];

// Сортируем массивы по timestamp
usort($statusHistory, function ($a, $b) {
    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
});
usort($employeeHistory, function ($a, $b) {
    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
});

// Инициализируем результирующий массив
$result = [];
$currentEmployee = null;
$currentStatus = null;

// Проходим по массивам и объединяем их
foreach ($statusHistory as $key => $item) {
    if ($key < count($employeeHistory)) {
        $currentEmployee = $employeeHistory[$key]['employee'];
    } else {
        $currentEmployee = end($employeeHistory)['employee'];
    }

    // Если статус изменился, обновляем информацию в результирующем массиве
    if ($item['status'] !== $currentStatus) {
        $result[$item['status']] = $currentEmployee;
        $currentStatus = $item['status'];
    }
}

// Выводим результат
print_r($result);
