<?php

/**
 * Объединяет историю статусов и историю изменений сотрудников задачи.
 *
 * @param array $statusHistory Массив с историей timestamp статусов задачи.
 *                             Каждый элемент - массив: ['timestamp' => int, 'status' => string].
 *                             Возможные значения status: 'backlog', 'analitycs', 'development', 'testing', 'done'.
 * @param array $employeeHistory Массив с историей timestamp изменений сотрудников задачи.
 *                               Каждый элемент - массив: ['timestamp' => int, 'employee' => string].
 *
 * @return array Объединенный массив, где каждый элемент представляет собой событие изменения статуса или сотрудника,
 *               отсортированный по времени.
 *               Каждый элемент массива имеет структуру: ['timestamp' => int, 'type' => string, 'data' => array].
 *               'type' может быть 'status' или 'employee'.
 */
function mergeStatusAndEmployeeHistory(array $statusHistory, array $employeeHistory): array
{
    // Сортируем массивы по времени.  Важно, чтобы массивы были отсортированы.
    usort($statusHistory, function ($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    usort($employeeHistory, function ($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    $mergedHistory = [];
    $statusIndex = 0;
    $employeeIndex = 0;

    while ($statusIndex < count($statusHistory) && $employeeIndex < count($employeeHistory)) {
        $statusEvent = $statusHistory[$statusIndex];
        $employeeEvent = $employeeHistory[$employeeIndex];

        // Сравниваем timestamp, чтобы определить, какое событие добавить первым
        if ($statusEvent['timestamp'] <= $employeeEvent['timestamp']) {
            $mergedHistory[] = [
                'timestamp' => $statusEvent['timestamp'],
                'type' => 'status',
                'data' => $statusEvent['status'],
            ];
            $statusIndex++;
        } else {
            $mergedHistory[] = [
                'timestamp' => $employeeEvent['timestamp'],
                'type' => 'employee',
                'data' => $employeeEvent['employee'],
            ];
            $employeeIndex++;
        }
    }

    // Добавляем оставшиеся статусы
    while ($statusIndex < count($statusHistory)) {
        $mergedHistory[] = [
            'timestamp' => $statusHistory[$statusIndex]['timestamp'],
            'type' => 'status',
            'data' => $statusHistory[$statusIndex]['status'],
        ];
        $statusIndex++;
    }

    // Добавляем оставшихся сотрудников
    while ($employeeIndex < count($employeeHistory)) {
        $mergedHistory[] = [
            'timestamp' => $employeeHistory[$employeeIndex]['timestamp'],
            'type' => 'employee',
            'data' => $employeeHistory[$employeeIndex]['employee'],
        ];
        $employeeIndex++;
    }

    return $mergedHistory;
}


// Пример использования:
$statusHistory = [
    ['timestamp' => 1678886400, 'status' => 'backlog'], // 2023-03-15 00:00:00
    ['timestamp' => 1678890000, 'status' => 'analitycs'], // 2023-03-15 01:00:00
    ['timestamp' => 1678900000, 'status' => 'development'], // 2023-03-15 03:00:00
    ['timestamp' => 1678910000, 'status' => 'testing'], // 2023-03-15 05:00:00
];

$employeeHistory = [
    ['timestamp' => 1678887000, 'employee' => 'John Doe'], // 2023-03-15 00:10:00
    ['timestamp' => 1678895000, 'employee' => 'Jane Smith'], // 2023-03-15 01:50:00
    ['timestamp' => 1678905000, 'employee' => 'Peter Jones'], // 2023-03-15 03:50:00
];

$mergedHistory = mergeStatusAndEmployeeHistory($statusHistory, $employeeHistory);

// Вывод результата
echo "<pre>";
print_r($mergedHistory);
echo "</pre>";


/* Пояснения к коду:

1.  **`mergeStatusAndEmployeeHistory(array $statusHistory, array $employeeHistory): array`**:
    *   Функция принимает два массива: `$statusHistory` и `$employeeHistory`.
    *   Функция возвращает объединенный массив.
2.  **Сортировка:**
    *   `usort` используется для сортировки обоих массивов по полю `timestamp` в возрастающем порядке.  Это крайне важно для правильной работы алгоритма.
3.  **Алгоритм слияния:**
    *   Инициализируются индексы `$statusIndex` и `$employeeIndex` для перебора массивов.
    *   В цикле `while` сравниваются значения `timestamp` текущих событий из обоих массивов.
    *   Если `timestamp` текущего статуса меньше или равен `timestamp` текущего сотрудника, то событие статуса добавляется в `$mergedHistory` первым.
    *   В противном случае добавляется событие изменения сотрудника.
    *   Инкрементируются соответствующие индексы.
4.  **Обработка остатков:**
    *   После завершения основного цикла `while` могут остаться необработанные события из одного из массивов.
    *   Два отдельных цикла `while` добавляют оставшиеся события из обоих массивов в `$mergedHistory`.
5.  **Возврат результата:**
    *   Функция возвращает `$mergedHistory`, содержащий объединенные события, отсортированные по времени.
6.  **Пример использования:**
    *   Создаются примеры массивов `$statusHistory` и `$employeeHistory`.
    *   Вызывается функция `mergeStatusAndEmployeeHistory()` для объединения массивов.
    *   `print_r()` используется для вывода результата в удобочитаемом формате.

**Важные моменты:**

*   **Стабильность сортировки:**  Использование `usort` гарантирует, что порядок элементов с одинаковыми `timestamp` не изменится (при условии, что функция сравнения стабильна).
*   **Обработка ошибок:** В реальном коде следует добавить обработку ошибок, например, проверку на пустые массивы или неверный формат данных.
*   **Масштабируемость:**  Если массивы очень большие, следует рассмотреть оптимизацию алгоритма, например, использование более эффективных структур данных.
*   **Тестирование:**  Рекомендуется тщательно протестировать код с различными сценариями, чтобы убедиться в его корректности.
*   **Валидация входных данных:**  В продакшн-коде важно добавить валидацию данных, чтобы убедиться, что значения статусов и имена сотрудников соответствуют ожидаемым значениям.
*   **Типизация:**  Использование типизации (`array`, `string`, `int`) помогает сделать код более надежным и понятным.
*   **Комментарии:** Добавлены подробные комментарии для облегчения понимания кода.
 *
 */