<?php
session_start();
require './headers/admin_header.phtml'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.phtml');
    exit;
}

require_once('../Models/connectionDB.php');
require_once('../Models/User.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);
$users = $userModel->getAllUsers();
$i = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card-box {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 25px;
      margin-bottom: 30px;
    }
    .btn-action {
      padding: 3px 10px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-semibold">🛠️ Admin Panel - Manage Users</h3>
      <a href="/Borrow_My_Charger/admin_dashboard.php" class="btn btn-dark">← Back to Dashboard</a>
    </div>

    <div class="card-box">
      <h5 class="mb-3">👥 All Registered Users</h5>

      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role_name']) ?></td>
              <td>
                <?php if ($user['status_id'] == 1): ?>
                  <span class="badge bg-success">Active</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Deactivated</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="../manage_users_controller.php?delete=<?= $user["user_id"] ?>" class="btn btn-sm btn-danger btn-action">Delete</a>
                <?php if ($user["status_id"] == 1): ?>
                  <a href="../manage_users_controller.php?deactivate=<?= $user["user_id"] ?>" class="btn btn-sm btn-warning btn-action">Deactivate</a>
                <?php else: ?>
                  <a href="../manage_users_controller.php?activate=<?= $user["user_id"] ?>" class="btn btn-sm btn-success btn-action">Activate</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
