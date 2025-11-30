<!DOCTYPE html>
<html>
    <body>
    <h1>Hello from WebApp</h1>
    <?php
    $conn = new mysqli("${DBServer.PrivateIp}", "admin", "${DBPassword}", "appdb");
    if ($conn->connect_error) { 
        echo "<h2>DB Connection Error!</h2>";
        // Display the raw error for troubleshooting
        echo "Error details: " . $conn->connect_error;
    } else {
        $result = $conn->query("SELECT text FROM message LIMIT 1");
        if ($row = $result->fetch_assoc()) {
        echo "<h2>DB message: " . $row["text"] . "</h2>";
        } else {
        echo "<h2>DB Message: No message found.</h2>";
        }
    }
    ?>
    </body>
</html>
