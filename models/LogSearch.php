<?php

namespace app\models;

use yii\base\Model;
use yii\db\Query;
use Yii;

class LogSearch extends Model
{
    public $from;
    public $to;
    public $os;
    public $architecture;

    public function rules()
    {
        return [
            [['from', 'to', 'os', 'architecture'], 'safe'],
        ];
    }

    private function applyFilters($query)
    {
        if ($this->from) {
            $query->andWhere(['>=', 'DATE(requested_at)', $this->from]);
        }
        if ($this->to) {
            $query->andWhere(['<=', 'DATE(requested_at)', $this->to]);
        }
        if ($this->os) {
            $query->andWhere(['os' => $this->os]);
        }
        if ($this->architecture) {
            $query->andWhere(['architecture' => $this->architecture]);
        }
    }

    public function getRequestsByDate()
    {
        $query = (new Query())
            ->select(['DATE(requested_at) as date', 'COUNT(*) as total'])
            ->from('logs')
            ->groupBy('DATE(requested_at)')
            ->orderBy('date');

        $this->applyFilters($query);
        return $query->all();
    }

    public function getTopBrowsers()
    {
        $query = (new Query())
            ->select(['browser', 'COUNT(*) as total'])
            ->from('logs')
            ->groupBy('browser')
            ->orderBy(['total' => SORT_DESC])
            ->limit(3);

        $this->applyFilters($query);
        $rows = $query->all();

        $grandTotal = array_sum(array_column($rows, 'total'));

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'browser' => $row['browser'],
                'total' => $row['total'],
                'percent' => $grandTotal > 0 ? round(($row['total'] / $grandTotal) * 100, 2) : 0
            ];
        }

        return $result;
    }

    public function getTableData($sortColumn = 'date', $sortOrder = 'DESC')
    {
        $datesQuery = (new Query())
            ->select(["DATE(requested_at) AS date", "COUNT(*) AS requests"])
            ->from('logs');

        $this->applyFilters($datesQuery);
        $datesQuery->groupBy("DATE(requested_at)");
        $dailyStats = $datesQuery->all();

        if (empty($dailyStats)) {
            return [];
        }

        $result = [];
        foreach ($dailyStats as $stat) {
            $date = $stat['date'];

            $urlQuery = (new Query())
                ->select(['url', 'COUNT(*) as cnt'])
                ->from('logs')
                ->where(['DATE(requested_at)' => $date]);
            $this->applyFilters($urlQuery);
            $topUrl = $urlQuery->groupBy('url')
                ->orderBy(['cnt' => SORT_DESC])
                ->limit(1)
                ->one();

            $browserQuery = (new Query())
                ->select(['browser', 'COUNT(*) as cnt'])
                ->from('logs')
                ->where(['DATE(requested_at)' => $date]);
            $this->applyFilters($browserQuery);
            $topBrowser = $browserQuery->groupBy('browser')
                ->orderBy(['cnt' => SORT_DESC])
                ->limit(1)
                ->one();

            $result[] = [
                'date' => $date,
                'requests' => $stat['requests'],
                'top_url' => $topUrl ? $topUrl['url'] : '-',
                'top_browser' => $topBrowser ? $topBrowser['browser'] : '-'
            ];
        }

        usort($result, function($a, $b) use ($sortColumn, $sortOrder) {
            if ($sortColumn == 'date') {
                $cmp = strtotime($a['date']) <=> strtotime($b['date']);
            } elseif ($sortColumn == 'requests') {
                $cmp = $a['requests'] <=> $b['requests'];
            } elseif ($sortColumn == 'top_url') {
                $cmp = strcmp($a['top_url'], $b['top_url']);
            } elseif ($sortColumn == 'top_browser') {
                $cmp = strcmp($a['top_browser'], $b['top_browser']);
            } else {
                $cmp = 0;
            }

            return $sortOrder === 'ASC' ? $cmp : -$cmp;
        });

        return $result;
    }

    public static function getUniqueOs()
    {
        return (new Query())
            ->select(['os'])
            ->from('logs')
            ->where(['not', ['os' => null]])
            ->andWhere(['!=', 'os', ''])
            ->groupBy('os')
            ->orderBy('os')
            ->all();
    }

    public static function getUniqueArchitectures()
    {
        return (new Query())
            ->select(['architecture'])
            ->from('logs')
            ->where(['not', ['architecture' => null]])
            ->andWhere(['!=', 'architecture', ''])
            ->groupBy('architecture')
            ->orderBy('architecture')
            ->all();
    }
}
