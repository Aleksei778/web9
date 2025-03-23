<?php

require_once 'core/base_active_record.php';

class TestModel extends BaseActiveRecord {
    protected $tableName = 'discipline_test';

    public function __construct() {
        parent::__construct();
    }

    public static function saveResult($fullName, $answers, $isCorrect) {
        $result = new self();
        $result->full_name = $fullName;
        $result->answers = json_encode($answers);
        $result->is_correct = $isCorrect;
        $result->created_at = date('Y-m-d H:i:s');
        
        return $result->save();
    }

    public static function getAllTestResults() {
        $instance = new self();
        echo $instance->tableName;
        $sql = "SELECT * FROM {$instance->tableName} ORDER BY created_at DESC";
        $stmt = $instance->pdo->prepare($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $results[0];
        $tests = [];
        foreach ($results as $result) {
            echo $result;
            $test = new self();

            $test->id = $result['id'];
            unset($result['id']);
            $test->attributes = $result;

            $tests[] = $test;
            echo $test;
        }
        echo $tests[0];
        return $tests;
    }
}