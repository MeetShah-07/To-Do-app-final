<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/TaskManager.php';
use PDO;

class TaskManagerTest extends TestCase
{
    protected $pdo;
    protected $taskManager;

    
    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=sql12.freesqldatabase.com;dbname=sql12743168', 'sql12743168', 'TnRn5yDYvX');
        $this->taskManager = new TaskManager($this->pdo);
    }

    
    public function testAddTask()
    {
        $task = "Test Task";

      
        $result = $this->taskManager->addTask($task);

        $this->assertTrue($result, "Task was not added successfully.");
    }

  
    public function testDeleteTask()
    {
      
        $taskId = 1;

       
        $result = $this->taskManager->deleteTask($taskId);

        $this->assertTrue($result, "Task was not deleted successfully.");
    }

    
    public function testUpdateTaskStatus()
    {
       
        $taskId = 1;
        $status = 'completed';

       
        $result = $this->taskManager->updateTaskStatus($taskId, $status);

        $this->assertTrue($result, "Task status was not updated successfully.");
    }

  
    public function testFilterTasksByStatus()
    {
       
        $status = 'pending';

        $tasks = $this->taskManager->getTasksByStatus($status);

        
        foreach ($tasks as $task) {
            $this->assertEquals($status, $task['status'], "Task status should be '$status'.");
        }
    }

   
    protected function tearDown(): void
    {
        
        $this->pdo = null;
    }
}
?>

