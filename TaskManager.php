<?php
class TaskManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addTask($task)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (task, status) VALUES (:task, 'pending')");
        return $stmt->execute(['task' => $task]);
    }

    public function deleteTask($taskId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $taskId]);
    }

    public function updateTaskStatus($taskId, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $taskId]);
    }

    public function getTasksByStatus($status)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
