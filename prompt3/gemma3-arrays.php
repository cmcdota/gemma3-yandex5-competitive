<?php

/**
 * Объединяет историю timestamp статусов задачи и историю timestamp изменения сотрудников.
 *
 * @param array $statusHistory Массив с историей timestamp статусов задачи.
 *                           Пример: [ [ 'timestamp' => 1678886400, 'status' => 'backlog' ], [ 'timestamp' => 1678890000, 'status' => 'analitycs' ] ]
 * @param array $employeeHistory Массив с историей timestamp изменения сотрудников в задаче.
 *                            Пример: [ [ 'timestamp' => 1678888200, 'employee' => 'John Doe' ], [ 'timestamp' => 1678889400, 'employee' => 'Jane Smith' ] ]
 *
 * @return array Объединенный массив с информацией о сотрудниках, работавших на каждой стадии.
 */
function combineHistory(array $statusHistory, array $employeeHistory): array
{
    // Сортируем массивы по времени.
    sort($statusHistory, SORT_NUMERIC);
    sort($employeeHistory, SORT_NUMERIC);

    $combinedHistory = [];
    $statusIndex = 0;
    $employeeIndex = 0;

    while ($statusIndex < count($statusHistory) && $employeeIndex < count($employeeHistory)) {
        $statusTimestamp = $statusHistory[$statusIndex]['timestamp'];
        $employeeTimestamp = $employeeHistory[$employeeIndex]['timestamp'];

        if ($statusTimestamp <= $employeeTimestamp) {
            // Событие статуса произошло раньше или одновременно с событием сотрудника
            $combinedHistory[] = $statusHistory[$statusIndex];
            $statusIndex++;
        } else {
            // Событие сотрудника произошло раньше события статуса
            $combinedHistory[] = $employeeHistory[$employeeIndex];
            $employeeIndex++;
        }
    }

    // Добавляем оставшиеся события статуса, если они есть.
    while ($statusIndex < count($statusHistory)) {
        $combinedHistory[] = $statusHistory[$statusIndex];
        $statusIndex++;
    }

    // Добавляем оставшиеся события сотрудника, если они есть.
    while ($employeeIndex < count($employeeHistory)) {
        $combinedHistory[] = $employeeHistory[$employeeIndex];
        $employeeIndex++;
    }

    return $combinedHistory;
}


/**
 *  Функция для удобства визуализации результата (не обязательна для решения задачи).
 *  Группирует события по времени и выводит информацию о сотруднике и статусе.
 */
function visualizeCombinedHistory(array $combinedHistory): void
{
    echo "Combined History:\n";
    foreach ($combinedHistory as $event) {
        if (isset($event['status'])) {
            echo "Timestamp: " . $event['timestamp'] . " - Status: " . $event['status'] . "\n";
        } elseif (isset($event['employee'])) {
            echo "Timestamp: " . $event['timestamp'] . " - Employee: " . $event['employee'] . "\n";
        }
    }
}

// Пример использования:
$statusHistory = [
    ['timestamp' => 1678886400, 'status' => 'backlog'],
    ['timestamp' => 1678890000, 'status' => 'analitycs'],
    ['timestamp' => 1678893600, 'status' => 'development'],
    ['timestamp' => 1678897200, 'status' => 'testing'],
    ['timestamp' => 1678900800, 'status' => 'done'],
];

$employeeHistory = [
    ['timestamp' => 1678888200, 'employee' => 'John Doe'],
    ['timestamp' => 1678889400, 'employee' => 'Jane Smith'],
    ['timestamp' => 1678892000, 'employee' => 'Peter Jones'],
];


$combinedHistory = combineHistory($statusHistory, $employeeHistory);
visualizeCombinedHistory($combinedHistory);


/* Пояснения к коду:*

1.  **`combineHistory(array $statusHistory, array $employeeHistory): array`**:
    *   Принимает два массива: `statusHistory` и `employeeHistory`.
    *   Сортирует оба массива по полю `timestamp` в числовом порядке (важно для корректного слияния событий).
    *   Инициализирует два указателя (`$statusIndex` и `$employeeIndex`) для перебора массивов.
    *   В цикле `while` сравнивает метки времени событий статуса и сотрудника.
    *   Если метка времени события статуса меньше или равна метке времени события сотрудника, добавляет событие статуса в `combinedHistory` и увеличивает `$statusIndex`.
    *   В противном случае добавляет событие сотрудника в `combinedHistory` и увеличивает `$employeeIndex`.
    *   После окончания основного цикла добавляет оставшиеся события из каждого массива (если они есть).
    *   Возвращает объединенный массив `combinedHistory`.

2.  **`visualizeCombinedHistory(array $combinedHistory): void`**:
    *   Это вспомогательная функция для вывода содержимого `combinedHistory` в удобном формате.  Она не обязательна для решения основной задачи.
    *   Проходит по всем элементам `combinedHistory` и выводит информацию о статусе или сотруднике в зависимости от того, какие поля содержатся в элементе.

**Как это работает:**

Алгоритм объединяет события, сортируя их по времени и перебирая их последовательно.  Он учитывает, что события статуса и события смены сотрудников могут происходить в разное время, и правильно их объединяет, чтобы получить полную хронологию работы над задачей.  По
сути, это слияние двух отсортированных списков (массивов).

**Важные моменты:**

*   **Сортировка:**  Обязательно сортировка по `timestamp`, так как порядок событий имеет решающее значение.
*   **Обработка разных типов событий:** Код должен уметь различать события статуса и события смены сотрудника и обрабатывать их соответствующим образом.
*   **Обработка пустых массивов:**  Код должен корректно работать, даже если один из массивов пуст.  В текущей реализации это предусмотрено.
*   **Типы данных:** Массивы должны содержать поля `timestamp`, `status` (для статусов) и `employee` (для сотрудников).  Код предполагает именно такую структуру.
*   **Временные зоны:**  В реальном проекте необходимо учитывать временные зоны, чтобы метки времени были интерпретированы правильно.  В этом примере это не учитывается, предполагается, что метки времени находятся в одной и той же временной зоне.
*  **Масштабируемость:** Если у вас очень большие массивы истории, возможно потребуется оптимизация алгоритма, например, использование более эффективных структур данных или алгоритмов.

Этот код представляет собой базовое решение задачи.  В реальных проектах может потребоваться более сложная логика для обработки различных сценариев и требований.
*/
