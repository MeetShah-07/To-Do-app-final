<?php
class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addTask($task) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO tasks (task, status) VALUES (:task, 'pending')");
            $stmt->bindParam(':task', $task);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error adding task: " . $e->getMessage();
        }
    }

    public function updateTaskContent($taskId, $newContent) {
        try {
            $stmt = $this->pdo->prepare("UPDATE tasks SET task = :new_content WHERE id = :id");
            $stmt->bindParam(':new_content', $newContent);
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error updating task: " . $e->getMessage();
        }
    }

    public function updateStatus($taskId, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error updating task status: " . $e->getMessage();
        }
    }

    public function deleteTask($taskId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error deleting task: " . $e->getMessage();
        }
    }

    public function getTasks($filter, $limit, $offset) {
        $query = "SELECT * FROM tasks";

        if ($filter !== 'all') {
            $query .= " WHERE status = :filter";
        }

        $query .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);

        if ($filter !== 'all') {
            $stmt->bindParam(':filter', $filter, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTasks() {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tasks");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Error counting tasks: " . $e->getMessage();
        }
    }
}
