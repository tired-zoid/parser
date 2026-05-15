<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $searchModel app\models\LogSearch */
/** @var $requestsByDate array */
/** @var $topBrowsers array */
/** @var $tableData array */
/** @var $osList array */
/** @var $archList array */
/** @var $sortColumn string */
/** @var $sortOrder string */

$this->title = 'Аналитика логов';

function sortLink($column, $label, $currentSort, $currentOrder) {
    $newOrder = 'ASC';
    if ($currentSort === $column && $currentOrder === 'ASC') {
        $newOrder = 'DESC';
    }

    // Сохраняем все текущие параметры GET
    $params = Yii::$app->request->get();
    $params['sort'] = $column;
    $params['order'] = $newOrder;

    $url = Url::to(['index'] + $params);

    $arrow = '';
    if ($currentSort === $column) {
        $arrow = $currentOrder === 'ASC' ? ' ↑' : ' ↓';
    }

    return '<a href="' . $url . '">' . $label . $arrow . '</a>';
}
?>

<h1><?= Html::encode($this->title) ?></h1>

<!-- ФИЛЬТРЫ -->
<div style="background: #f5f5f5; padding: 15px; margin-bottom: 20px;">
    <form method="get">
        <label>Дата от:</label>
        <input type="date" name="from" value="<?= $searchModel->from ?>">

        <label>Дата до:</label>
        <input type="date" name="to" value="<?= $searchModel->to ?>">

        <label>ОС:</label>
        <select name="os">
            <option value="">Все</option>
            <?php foreach ($osList as $os): ?>
                <option value="<?= Html::encode($os['os']) ?>" <?= $searchModel->os == $os['os'] ? 'selected' : '' ?>>
                    <?= Html::encode($os['os']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Архитектура:</label>
        <select name="architecture">
            <option value="">Все</option>
            <?php foreach ($archList as $arch): ?>
                <option value="<?= Html::encode($arch['architecture']) ?>" <?= $searchModel->architecture == $arch['architecture'] ? 'selected' : '' ?>>
                    <?= Html::encode($arch['architecture']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Применить</button>
        <?= Html::a('Сбросить', ['index']) ?>
    </form>
</div>

<!-- ГРАФИК 1: Запросы по дням -->
<h2>Запросы по дням</h2>
<canvas id="requestsChart" style="height: 300px; width: 100%;"></canvas>

<!-- ГРАФИК 2: Топ браузеров -->
<h2>Топ-3 браузера</h2>
<canvas id="browserChart" style="height: 300px; width: 100%;"></canvas>

<!-- ТАБЛИЦА С СОРТИРОВКОЙ -->
<h2>Статистика по дням</h2>

<table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
    <thead>
    <tr>
        <th><?= sortLink('date', 'Дата', $sortColumn, $sortOrder) ?></th>
        <th><?= sortLink('requests', 'Кол-во запросов', $sortColumn, $sortOrder) ?></th>
        <th><?= sortLink('top_url', 'Самый популярный URL', $sortColumn, $sortOrder) ?></th>
        <th><?= sortLink('top_browser', 'Самый популярный браузер', $sortColumn, $sortOrder) ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tableData as $row): ?>
        <tr>
            <td><?= $row['date'] ?></td>
            <td><?= $row['requests'] ?></td>
            <td><?= Html::encode($row['top_url']) ?></td>
            <td><?= Html::encode($row['top_browser']) ?></td>
        </tr>
    <?php endforeach; ?>

    <?php if (empty($tableData)): ?>
        <tr>
            <td colspan="4" style="text-align: center;">Нет данных</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- СКРИПТЫ ГРАФИКОВ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const requestsData = <?= json_encode([
        'labels' => array_column($requestsByDate, 'date'),
        'data' => array_column($requestsByDate, 'total')
    ]) ?>;

    new Chart(document.getElementById('requestsChart'), {
        type: 'line',
        data: {
            labels: requestsData.labels,
            datasets: [{
                label: 'Количество запросов',
                data: requestsData.data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    const browserData = <?= json_encode($topBrowsers) ?>;

    new Chart(document.getElementById('browserChart'), {
        type: 'bar',
        data: {
            labels: browserData.map(item => item.browser),
            datasets: [{
                label: 'Процент запросов',
                data: browserData.map(item => item.percent),
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    title: {
                        display: true,
                        text: 'Процент (%)'
                    },
                    max: 100
                }
            }
        }
    });
</script>

<style>
    form > * {
        margin-right: 10px;
        margin-bottom: 10px;
    }

    button, .btn {
        padding: 5px 10px;
        cursor: pointer;
    }

    table th {
        background: #f5f5f5;
        padding: 10px;
    }

    table th a {
        text-decoration: none;
        color: #333;
        display: block;
    }

    table th a:hover {
        color: #007bff;
    }

    table td {
        padding: 8px;
    }

    h2 {
        margin-top: 30px;
    }
</style>
