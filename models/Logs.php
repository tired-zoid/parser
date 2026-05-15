<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property string $ip
 * @property string $requested_at
 * @property string $url
 * @property string $user_agent
 * @property string|null $os
 * @property string|null $architecture
 * @property string|null $browser
 */
class Logs extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['os', 'architecture', 'browser'], 'default', 'value' => null],
            [['ip', 'requested_at', 'url', 'user_agent'], 'required'],
            [['requested_at'], 'safe'],
            [['user_agent'], 'string'],
            [['ip'], 'string', 'max' => 45],
            [['url'], 'string', 'max' => 2048],
            [['os', 'browser'], 'string', 'max' => 50],
            [['architecture'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'requested_at' => 'Requested At',
            'url' => 'Url',
            'user_agent' => 'User Agent',
            'os' => 'Os',
            'architecture' => 'Architecture',
            'browser' => 'Browser',
        ];
    }

}
