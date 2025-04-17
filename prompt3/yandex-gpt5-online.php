<?php

// Пример исходных массивов
$statusHistory = [
    ['timestamp' => 1614556800, 'status' => 'backlog'], // 1 февраля 2021
    ['timestamp' => 1614643200, 'status' => 'analitycs'], // 2 февраля 2021
    ['timestamp' => 1614729600, 'status' => 'development'], // 3 февраля 2021
    ['timestamp' => 1614816000, 'status' => 'testing'], // 4 февраля 2021
    ['timestamp' => 1614902400, 'status' => 'done'] // 5 февраля 2021
];

$employeeHistory = [
    ['timestamp' => 1614556800, 'employee' => 'сотрудник 1'], // 1 февраля 2021
    ['timestamp' => 1614630000, 'employee' => 'сотрудник 2'], // 2 февраля 2021 (до перехода на стадию analitycs)
    ['timestamp' => 1614720000, 'employee' => 'сотрудник 3'], // 3 февраля 2021 (после перехода на стадию development)
    ['timestamp' => 1614806400, 'employee' => 'сотрудник 4'] // 4 февраля 2021 (после перехода на стадию testing)
];

// Функция для объединения массивов по времени
function mergeHistories($statusHistory, $employeeHistory) {
    $mergedHistory = [];
    $currentEmployees = [];

    // Объединяем массивы по времени
    foreach ($statusHistory as $status) {
        // Добавляем текущие статусы в объединённый массив
        $mergedHistory[$status['status']] = ['timestamp' => $status['timestamp'], 'employees' => $currentEmployees];

        // Проверяем, есть ли изменения сотрудников в этот период
        foreach ($employeeHistory as $key => $employee) {
            if ($employee['timestamp'] <= $status['timestamp']) {
                $currentEmployees[] = $employee['employee'];
                unset($employeeHistory[$key]); // Удаляем обработанного сотрудника из массива
            } else {
                break; // Если время сотрудника больше текущего статуса, прерываем цикл
            }
        }
    }

    return $mergedHistory;
}

// Выводим результат
$result = mergeHistories($statusHistory, $employeeHistory);
print_r($result);

?>