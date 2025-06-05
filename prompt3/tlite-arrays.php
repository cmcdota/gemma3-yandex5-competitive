<?php

function getTaskHistory($statusHistory, $employeeHistory) {
    // Создаем массив для хранения истории задач с информацией о статусах и сотрудниках.
    $taskHistory = [];

    // Массив со всеми возможными стадиями задачи.
    $statuses = ['backlog', 'analytics', 'development', 'testing', 'done'];

    // Проходим по каждой записи в истории статусов.
    foreach ($statusHistory as $statusEvent) {
        list($timestamp, $status, $employeeIds) = $statusEvent;

        // Проверяем, есть ли запись о текущем статусе в массиве taskHistory.
        if (!isset($taskHistory[$timestamp])) {
            $taskHistory[$timestamp] = [];
        }

        // Обновляем информацию о текущей стадии для данного времени.
        $taskHistory[$timestamp][$status] = $employeeIds;
    }

    // Проходим по каждой записи в истории сотрудников, чтобы добавить информацию о смене сотрудников на разных этапах.
    foreach ($employeeHistory as $employeeEvent) {
        list($timestamp, $taskId, $employeeId) = $employeeEvent;

        if (!isset($taskHistory[$timestamp])) {
            continue; // Пропускаем, если времени нет в taskHistory
        }

        // Обновляем информацию о сотрудниках на текущей стадии.
        foreach ($statuses as $status) {
            if (isset($taskHistory[$timestamp][$status]) && in_array($employeeId,
$taskHistory[$timestamp][$status])) {
                // Если сотрудник уже присутствует в списке для этой стадии, ничего не делаем.
                continue;
            }

            // Добавляем сотрудника к текущей стадии, если он новый.
            if (!isset($taskHistory[$timestamp][$status]) || !in_array($employeeId,
$taskHistory[$timestamp][$status])) {
                $taskHistory[$timestamp][$status][] = $employeeId;
            }
        }
    }

    // Формируем окончательный результат в удобном для чтения формате.
    $result = [];
    foreach ($taskHistory as $timestamp => $events) {
        $result[] = [
            'timestamp' => $timestamp,
            'history' => [],
        ];

        foreach ($statuses as $status) {
            if (isset($events[$status])) {
                // Объединяем всех сотрудников на текущей стадии.
                $result[count($result) - 1]['history'][$status] = implode(', ', $events[$status]);
            }
        }
    }

    return $result;
}

// Пример использования функции:
$statusHistory = [
    [1630521600, 'backlog', ['employee1']],
    [1630608000, 'analytics', ['employee2']],
    [1630790400, 'development', ['employee1', 'employee3']],
    [1630963200, 'testing', ['employee2']],
    [1631136000, 'done', []],
];

$employeeHistory = [
    [1630521600, 1, 'employee1'],
    [1630608000, 1, 'employee2'],
    [1630790400, 1, 'employee3'],
    [1630963200, 1, 'employee2'],
];

$result = getTaskHistory($statusHistory, $employeeHistory);

// Выводим результат
foreach ($result as $item) {
    echo "Timestamp: {$item['timestamp']}\n";
    foreach ($item['history'] as $status => $employees) {
        echo "  {$status}: {$employees}\n";
    }
    echo "\n";
}


/*
### Объяснение кода:

1. **Функция `getTaskHistory`** принимает два массива: `$statusHistory` и `$employeeHistory`. Каждый из них
содержит записи о времени, статусе задачи или изменении сотрудников.

2. **Инициализация `$taskHistory`** - это массив, в котором ключом является время, а значением - информация о
текущем статусе и сотрудниках на этом этапе.

3. **Первая проходка по истории статусов** добавляет информацию о каждом статусе и соответствующих сотрудниках для
каждого времени.

4. **Вторая проходка по истории сотрудников** обновляет список сотрудников на каждой стадии, если они изменились
или были впервые назначены.

5. **Формирование окончательного результата** - объединяем информацию о статусах и сотрудниках в удобном для
чтения формате с использованием временных меток.

Этот код позволяет объединить информацию из двух массивов и получить подробную историю изменений задачи с
указанием сотрудников на каждой стадии.

total duration:       2m17.4078555s
load duration:        29.8977ms
prompt eval count:    266 token(s)
prompt eval duration: 783.4687ms
prompt eval rate:     339.52 tokens/s
eval count:           1202 token(s)
eval duration:        2m16.5942465s
eval rate:            8.80 tokens/s
*/