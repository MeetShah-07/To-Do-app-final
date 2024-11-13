<?php
include 'db.php';
include 'Task.php';

$taskManager = new Task($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    // Adding a new task
    $task = trim($_POST['task']);
    if (empty($task) || strlen($task) > 255) {
        echo "Task cannot be empty and must be less than 255 characters.";
        exit;
    }

    $taskManager->addTask($task);
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id']) && isset($_POST['new_content'])) {
    // Updating an existing task content
    $taskId = filter_var($_POST['update_task_id'], FILTER_VALIDATE_INT);
    $newContent = trim($_POST['new_content']);

    if ($taskId === false) {
        echo "Invalid task ID.";
        exit;
    }

    try {
        $taskManager->updateTaskContent($taskId, $newContent);
        echo "Task content updated successfully.";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['status'])) {
    // Updating the status of a task
    $taskId = filter_var($_POST['task_id'], FILTER_VALIDATE_INT);
    if ($taskId === false) {
        echo "Invalid task ID.";
        exit;
    }

    $taskManager->updateStatus($taskId, $_POST['status']);
    exit;
}

if (isset($_GET['delete'])) {
    // Deleting a task
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id === false) {
        echo "Invalid task ID.";
        exit;
    }

    $taskManager->deleteTask($id);
    header("Location: index.php");
    exit;
}

$filter = $_GET['filter'] ?? 'all';
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$tasks = $taskManager->getTasks($filter, $limit, $offset);
$totalTasks = $taskManager->countTasks();
$totalPages = ceil($totalTasks / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <!-- Form to add a new task -->
        <form method="POST" action="index.php">
            <input type="text" name="task" placeholder="Enter new task" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Filter links -->
        <div class="filters">
            <a href="index.php?filter=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="index.php?filter=pending" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="index.php?filter=completed" class="<?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
        </div>

        <!-- List of tasks -->
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="<?php echo htmlspecialchars($task['status']); ?>">
                    <input type="checkbox" 
                           class="status-checkbox" 
                           data-task-id="<?php echo htmlspecialchars($task['id']); ?>" 
                           <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                    <span class="task-text <?php echo htmlspecialchars($task['status']); ?>">
                        <?php echo htmlspecialchars($task['task']); ?>
                    </span>

                    <!-- Link to delete the task -->
                    <a href="index.php?delete=<?php echo htmlspecialchars($task['id']); ?>" class="delete-btn">Delete</a>

                    <!-- Show Edit Button -->
                    <button type="button" class="show-update-btn">Edit</button>

                    <!-- Update Task Form -->
                    <form method="POST" action="index.php" class="update-task-form" style="display: none;">
                        <input type="hidden" name="update_task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                        <input type="text" name="new_content" value="<?php echo htmlspecialchars($task['task']); ?>" class="update-input">
                        <button type="submit" class="update-btn">Update</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Pagination links -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        // JavaScript to handle the toggle of edit buttons and form submission
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener to all edit buttons
            document.querySelectorAll('.show-update-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const listItem = button.closest('li');
                    const updateForm = listItem.querySelector('.update-task-form');

                    // Toggle visibility of the update form
                    updateForm.style.display = updateForm.style.display === 'block' ? 'none' : 'block';
                });
            });
        });
    </script>
</body>
</html>
