<?php

function mergeHistory($statuses, $employees)
{
    // Создаем единый массив событий с временем
    $combinedEvents = array_merge(
        array_map(function ($item) { return ['time' => $item['timestamp'], 'type' => 'status', 'data' => $item]; }, $statuses),
        array_map(function ($item) { return ['time' => $item['timestamp'], 'type' => 'employee', 'data' => $item]; }, $employees)
    );

    // Сортируем события по времени
    usort($combinedEvents, function ($a, $b) {
        return $a['time'] <=> $b['time'];
    });

    // Инициализируем текущих сотрудников и результирующий массив стадий
    $currentEmployees = [];
    $result = [];

    foreach ($combinedEvents as $event) {
        if ($event['type'] === 'status') {
            // Обновляем информацию о статусе задачи, добавляя текущих сотрудников
            $stageName = $event['data']['status'];
            $result[$stageName] = [
                'start_time' => $event['time'],
                'employees' => array_values($currentEmployees) // Преобразуем в массив для вывода
            ];
        } elseif ($event['type'] === 'employee') {
            // Если сотрудник добавлен, добавляем его в текущий список
            if ($event['data']['action'] === 'add') {
                $currentEmployees[$event['data']['employee_id']] = $event['data']['employee_name'];
            }
            // Если удалили — убираем из списка
            elseif ($event['data']['action'] === 'remove' && isset($currentEmployees[$event['data']['employee_id']])) {
                unset($currentEmployees[$event['data']['employee_id']]);
            }
        }
    }

    return $result;
}

// Пример данных
$statusHistory = [
    ['timestamp' => 1609459200, 'status' => 'backlog'],
    ['timestamp' => 1609545600, 'status' => 'analitycs'],
    ['timestamp' => 1609632000, 'status' => 'development'],
    ['timestamp' => 1609718400, 'status' => 'testing'],
    ['timestamp' => 1609804800, 'status' => 'done']
];

$employeeHistory = [
    ['timestamp' => 1609459230, 'action' => 'add', 'employee_id' => 1, 'employee_name' => 'Сотрудник 1'],
    ['timestamp' => 1609545645, 'action' => 'add', 'employee_id' => 2, 'employee_name' => 'Сотрудник 2'],
    ['timestamp' => 1609545700, 'action' => 'remove', 'employee_id' => 1],
    ['timestamp' => 1609632100, 'action' => 'add', 'employee_id' => 3, 'employee_name' => 'Сотрудник 3'],
];

// Объединяем историю
$mergedHistory = mergeHistory($statusHistory, $employeeHistory);

// Выводим результат
foreach ($mergedHistory as $stage => $info) {
    echo "{$stage}: " . implode(', ', $info['employees']) . PHP_EOL;
}
