<?php
include 'dbcon.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: index.php?msg=error");
    exit();
}

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name !== "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );

        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?msg=updated");
            exit();
        }

        $stmt->close();
    }

    $error = "Please enter a valid name and email address.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">

    <div class="card">
        <div class="card-header bg-warning">
            Edit User
        </div>

        <div class="card-body">

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label class="form-label">Full Name</label>

                <input
                    type="text"
                    name="name"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($user['name']) ?>"
                    required
                >

                <label class="form-label">Email Address</label>

                <input
                    type="email"
                    name="email"
                    class="form-control mb-3"
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required
                >

                <button type="submit" name="update" class="btn btn-warning">
                    Update User
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>