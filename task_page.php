<?php

define('TASK_FILE', 'tasks.json');

// Function to load tasks from the file
function loadFunction(): array
{
    if (!file_exists(TASK_FILE)) {
        return [];
    }

    $data = file_get_contents(TASK_FILE);
    

    return $data ? json_decode($data, true) : [];
}

$tasks = loadFunction();

// Function to save tasks to the file
function saveTasks(array $tasks): void
{
    file_put_contents(TASK_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task']) && !empty(trim($_POST['task']))) {
        // Add task
        $tasks[] = [
            'task' => htmlspecialchars(trim($_POST['task'])),
            'done' => false
        ];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['delete'])) {
        // Delete task
        unset($tasks[$_POST['delete']]);
        $tasks = array_values($tasks); // Reindex the array
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['toggle'])) {
        // Toggle task completion
        $tasks[$_POST['toggle']]['done'] = !$tasks[$_POST['toggle']]['done'];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="styles.css"> -->
     <style>
        body {
    margin-top: 20px;
}

.task-card {
    border: 1px solid #ececec; 
    padding: 20px;
    border-radius: 5px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
}

.task {
    color: #888;
}

.task-done {
    text-decoration: line-through !important;
    color: #888 !important;
    text-decoration-thickness: 2px !important;
}

.task-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.task-toggle-btn {
    text-decoration: none !important;
    border: none !important;
    background: transparent !important;
    padding: 8px 0 !important;
    width: 100%;
    text-align: left;
}
     </style>
    <title>To-Do App</title>
</head>

<body>
    <div class="container">
        <div class="task-card">
            <h1>Add Your To-Do</h1>
            <!-- Add Task Form -->
            <form method="POST" class="row g-2">
                <div class="col-9">
                    <input type="text" name="task" class="form-control" placeholder="Enter your task" required>
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary w-100">Add Task</button>
                </div>
            </form>

            <!-- Task List -->
            <div class="mt-4">Task List</div>
            <ul class="list-unstyled">
    <?php if (empty($tasks)): ?>
        <li class="task-item d-flex align-items-center mb-2">
            <span class="task">No tasks found</span>
        </li>
    <?php else: ?>
        <?php foreach ($tasks as $index => $task): ?>
            <li class="task-item d-flex align-items-center mb-2">
                <!-- Task Text -->
                <form method="POST" class="flex-grow-1">
                    <input type="hidden" name="toggle" value="<?= $index ?>">
                    <button type="submit" class="task-toggle-btn">
                    <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                            <?= htmlspecialchars($task['task']) ?>
                        </span>
                    </button>
                </form>

                <!-- Delete Button -->
                <form method="POST" class="ms-2">
                    <input type="hidden" name="delete" value="<?= $index ?>">
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
              
             
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
