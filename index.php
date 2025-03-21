<?php
// Define the file that will store tasks.
$file = 'tasks.json';

// Load existing tasks if the file exists; otherwise, start with an empty array.
if (file_exists($file)) {
    $tasks = json_decode(file_get_contents($file), true);
} else {
    $tasks = [];
}

// Process adding a new task via POST request.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task'])) {
        $task = trim($_POST['task']);
        if ($task !== '') {
            // Add new task with a 'done' status set to false.
            $tasks[] = ['task' => $task, 'done' => false];
            file_put_contents($file, json_encode($tasks));
        }
    }
    // Redirect to avoid resubmission and reload the updated list.
    header("Location: index.php");
    exit;
}

// Process actions sent via GET (toggle and delete).
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $index  = isset($_GET['index']) ? (int)$_GET['index'] : -1;

    // Delete the task if the action is 'delete'.
    if ($action === 'delete' && isset($tasks[$index])) {
        unset($tasks[$index]);
        // Reindex the array.
        $tasks = array_values($tasks);
    }

    // Toggle the 'done' status if the action is 'toggle'.
    if ($action === 'toggle' && isset($tasks[$index])) {
        $tasks[$index]['done'] = !$tasks[$index]['done'];
    }

    // Save changes to the file and redirect.
    file_put_contents($file, json_encode($tasks));
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Simple To-Do App</title>
  <!-- Using Milligram for a clean, simple layout -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
  <style>
    /* Simple style to strike through completed tasks */
    .done { text-decoration: line-through; }
    ul { list-style-type: none; padding: 0; }
    li { padding: 0.5rem 0; }
    .actions { margin-left: 1rem; font-size: 0.9rem; }
  </style>
</head>
<body>
<div class="container" style="margin-top:50px;">
  <h1>Simple To-Do App</h1>
  
  <!-- Form to add a new task -->
  <form method="POST" action="">
    <input type="text" name="task" placeholder="Enter new task" required>
    <button type="submit">Add Task</button>
  </form>
  
  <!-- List of tasks -->
  <ul>
    <?php foreach ($tasks as $index => $item): ?>
      <li>
        <!-- Task text is a clickable link that toggles its status -->
        <a href="?action=toggle&index=<?php echo $index; ?>" class="<?php echo $item['done'] ? 'done' : ''; ?>">
          <?php echo htmlspecialchars($item['task'], ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <!-- Delete action with a simple confirmation -->
        <span class="actions">
          <a href="?action=delete&index=<?php echo $index; ?>" onclick="return confirm('Delete this task?');">Delete</a>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
</body>
</html>
