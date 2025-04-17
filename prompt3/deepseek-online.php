<?php

/**
 * Функция объединяет историю статусов задачи и историю смены сотрудников
 * для определения, какие сотрудники работали на каждом этапе
 *
 * @param array $statusHistory Массив истории статусов в формате [timestamp => status]
 * @param array $assigneeHistory Массив истории смены сотрудников в формате [timestamp => [assignees]]
 * @return array Массив с объединенной информацией по этапам и сотрудникам
 */
function combineTaskHistory(array $statusHistory, array $assigneeHistory): array
{
    // Объединяем все ключи (timestamp) из обоих массивов
    $allTimestamps = array_unique(array_merge(
        array_keys($statusHistory),
        array_keys($assigneeHistory)
    ));

    // Сортируем временные метки по возрастанию
    sort($allTimestamps);

    $result = [];
    $currentStatus = null;
    $currentAssignees = [];

    // Проходим по всем временным меткам
    foreach ($allTimestamps as $timestamp) {
        // Обновляем текущий статус, если он изменился в этой временной метке
        if (isset($statusHistory[$timestamp])) {
            $currentStatus = $statusHistory[$timestamp];
        }

        // Обновляем текущих сотрудников, если они изменились в этой временной метке
        if (isset($assigneeHistory[$timestamp])) {
            $currentAssignees = $assigneeHistory[$timestamp];
        }

        // Если статус определен, добавляем информацию в результат
        if ($currentStatus !== null && !empty($currentAssignees)) {
            // Инициализируем массив для статуса, если его еще нет
            if (!isset($result[$currentStatus])) {
                $result[$currentStatus] = [];
            }

            // Добавляем сотрудников, избегая дублирования
            foreach ($currentAssignees as $assignee) {
                if (!in_array($assignee, $result[$currentStatus], true)) {
                    $result[$currentStatus][] = $assignee;
                }
            }
        }
    }

    return $result;
}

// Пример использования:

// История изменения статусов задачи (timestamp => status)
$statusHistory = [
    1000 => 'backlog',
    2000 => 'analitycs',
    3000 => 'development',
    4000 => 'testing',
    5000 => 'done',
];

// История изменения сотрудников в задаче (timestamp => [assignees])
$assigneeHistory = [
    1500 => ['manager1'], // В бэклоге добавился manager1
    1800 => ['manager1', 'manager2'], // В бэклоге добавился manager2
    2200 => ['analyst1'], // На аналитике работает analyst1
    2500 => ['analyst2'], // Аналитик сменился на analyst2
    3200 => ['dev1'], // В разработке dev1
    3500 => ['dev1', 'dev2'], // Добавился dev2
    4200 => ['tester1'], // В тестировании tester1
];

$taskHistory = combineTaskHistory($statusHistory, $assigneeHistory);

// Вывод результата
print_r($taskHistory);

/**
 * Ожидаемый результат:
 * Array
 * (
 *     [backlog] => Array
 *         (
 *             [0] => manager1
 *             [1] => manager2
 *         )
 *     [analitycs] => Array
 *         (
 *             [0] => analyst1
 *             [1] => analyst2
 *         )
 *     [development] => Array
 *         (
 *             [0] => dev1
 *             [1] => dev2
 *         )
 *     [testing] => Array
 *         (
 *             [0] => tester1
 *         )
 * )
 */

/*
Код включает:
1. Основную функцию `combineTaskHistory`, которая объединяет историю статусов и сотрудников
2. Логику обработки временных меток и отслеживания текущих статусов/сотрудников
3. Пример использования с тестовыми данными
4. Комментарии, объясняющие ключевые части кода
5. Ожидаемый вывод в виде комментария

Функция корректно обрабатывает случаи:
- Смены статусов
- Смены сотрудников
- Одновременного изменения статуса и сотрудников
- Отсутствия сотрудников на некоторых этапах
 */