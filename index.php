
<?php
include 'dbcon.php';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email) VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $name, $email);

    if ($name != "" && filter_var($email, FILTER_VALIDATE_EMAIL) && $stmt->execute()) {
        header("Location: index.php?msg=added");
        exit();
    }

    $error = "Please enter a valid name and email.";
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">

    <h2 class="text-center mb-5">User Management System</h2>

    <?php
    $messages = [
        'added' => 'User added successfully!',
        'updated' => 'User updated successfully!',
        'deleted' => 'User deleted successfully!'
    ];

    if (isset($_GET['msg']) && isset($messages[$_GET['msg']])) {
        echo '<div class="alert alert-success">'
            . htmlspecialchars($messages[$_GET['msg']]) . '</div>';
    }

    if (isset($error)) {
        echo '<div class="alert alert-danger">'
            . htmlspecialchars($error) . '</div>';
    }
    ?>

    <div class="row">

        <!-- Add New User -->
        <div class="col-md-5">
            <h5 class="mb-3">Add New User</h5>

            <form method="POST">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control mb-3"
                       placeholder="Enter Full Name" required>

                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control mb-3"
                       placeholder="Enter Email Address" required>

                <button type="submit" name="submit"
                        class="btn btn-primary w-100">Submit</button>
            </form>
        </div>

        <!-- Registered Users -->
        <div class="col-md-7">
            <h5 class="mb-3">Registered Users</h5>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>

                            <a href="delete.php?id=<?= $row['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No data found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body>
</html>
