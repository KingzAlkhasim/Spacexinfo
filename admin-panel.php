<?php
/*
Template Name: Spacexinfo Admin Panel
Template Post Type: page
*/

session_start();

// 1. SECURITY CHECK
// If user is not logged in OR is not an admin, kick them out.
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    // Ideally, redirect to login. For now, we kill the page.
    // Note: You must ensure your Login script sets $_SESSION['role'] from the database!
    // If you haven't updated login.php yet, remove the "|| $_SESSION['role'] !== 'admin'" part temporarily to test.
    die("ACCESS DENIED: Admins only.");
}

// 2. DATABASE CONNECTION
$servername = "localhost";
$dbusername = "spacenet_spacexinfo"; 
$dbpassword = "@#passNet"; 
$dbname = "spacenet_spacexinfo_userdb";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// 3. HANDLE ACTIONS (Deposit, Suspend, Delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. Add Balance
    if (isset($_POST['add_balance'])) {
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $user_id);
        
        if ($stmt->execute()) {
            $message = "<div class='alert success'>Successfully added $$amount to User ID $user_id</div>";
        } else {
            $message = "<div class='alert error'>Error updating balance.</div>";
        }
        $stmt->close();
    }

    // B. Toggle Suspension
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status == 'active') ? 'suspended' : 'active';
        
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "<div class='alert success'>User status updated to $new_status</div>";
    }
}

// 4. FETCH ALL USERS
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #0a0e27; color: white; padding: 2rem; }
        
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; }
        .logo { background: linear-gradient(135deg, #00ff87, #00d4ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: bold; font-size: 1.5rem; }
        
        /* Table Styles */
        .user-table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
        .user-table th, .user-table td { padding: 1rem; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .user-table th { background: rgba(0,0,0,0.2); color: #00ff87; font-weight: 600; }
        .user-table tr:hover { background: rgba(255,255,255,0.05); }
        
        /* Badges */
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .badge-active { background: rgba(0, 255, 135, 0.2); color: #00ff87; }
        .badge-suspended { background: rgba(255, 71, 87, 0.2); color: #ff4757; }
        
        /* Buttons */
        .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; color: white; font-size: 0.85rem; margin-right: 5px; transition: 0.3s; }
        .btn-fund { background: #00d4ff; }
        .btn-suspend { background: #ff4757; }
        .btn-activate { background: #00ff87; color: #0a0e27; }
        .btn:hover { opacity: 0.8; }

        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: #1a1f3c; padding: 2rem; border-radius: 10px; width: 400px; border: 1px solid rgba(255,255,255,0.1); }
        .modal input { width: 100%; padding: 10px; margin: 10px 0; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 5px; }
        
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .success { background: rgba(0, 255, 135, 0.2); color: #00ff87; border: 1px solid #00ff87; }
        .error { background: rgba(255, 71, 87, 0.2); color: #ff4757; border: 1px solid #ff4757; }

        @media(max-width: 768px) {
            .user-table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <div class="logo">Spacexinfo Admin</div>
        <div>Welcome, <?php echo $_SESSION['username']; ?></div>
    </div>

    <?php echo $message; ?>

    <div style="overflow-x:auto;">
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Info</th>
                    <th>Role</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                                <small style="color:#b8bcc8"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td><?php echo $row['role']; ?></td>
                            <td style="font-weight:bold; color:#00ff87;">$<?php echo number_format($row['balance'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-fund" onclick="openFundModal(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>')">
                                    + Add Fund
                                </button>

                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $row['status']; ?>">
                                    <input type="hidden" name="toggle_status" value="1">
                                    
                                    <?php if($row['status'] == 'active'): ?>
                                        <button class="btn btn-suspend" onclick="return confirm('Suspend this user?');">Suspend</button>
                                    <?php else: ?>
                                        <button class="btn btn-activate">Activate</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center">No users found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="fundModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 1rem;">Add Funds to <span id="modalUsername" style="color:#00ff87"></span></h3>
            
            <form method="POST">
                <input type="hidden" name="add_balance" value="1">
                <input type="hidden" name="user_id" id="modalUserId">
                
                <label>Amount to Add ($)</label>
                <input type="number" name="amount" step="0.01" placeholder="e.g. 1000.00" required>
                
                <button type="submit" class="btn btn-activate" style="width:100%; padding: 10px; margin-top: 10px;">Confirm Deposit</button>
                <button type="button" class="btn btn-suspend" style="width:100%; padding: 10px; margin-top: 5px;" onclick="closeFundModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openFundModal(id, username) {
            document.getElementById('modalUserId').value = id;
            document.getElementById('modalUsername').textContent = username;
            document.getElementById('fundModal').style.display = 'flex';
        }

        function closeFundModal() {
            document.getElementById('fundModal').style.display = 'none';
        }
    </script>

</body>
</html>
<?php $conn->close(); ?>