<?php
include 'dbconnect.php';

$model = $_GET['model'] ?? '';
$year = $_GET['year'] ?? '';

$sql = "SELECT * FROM cars WHERE 1=1";
$params = [];
$types = "";

if (!empty($model)) {
    $sql .= " AND car_model LIKE ?";
    $params[] = "%$model%";
    $types .= "s";
}
if (!empty($year)) {
    $sql .= " AND year = ?";
    $params[] = $year;
    $types .= "s";
}

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search - CarNext</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .logo-img {
            position: absolute;
            top: 5px;
            left: 20px;
            width: 100px;
            height: auto;
            z-index: 999;
        }
        .links {
            background-color: gray;
            text-align: center;
            padding: 15px 10px;
            margin-top: 10px;
        }
        .links a {
            display: inline-block;
            color: white;
            font-size: 18px;
            margin: 5px 10px;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
        .links a:hover {
            background-color: darkgray;
            color: black;
        }
        .links a.active {
            background-color: darkgray;
            color: black;
        }
        .search-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 4px;
        }
        .search-bar input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            flex: 1;
            min-width: 120px;
        }
        .btn-search {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 30px;
        }
        .car-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        .car-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #ddd;
        }
        .car-info {
            padding: 15px;
        }
        .car-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .car-price {
            color: #007bff;
            font-weight: bold;
        }
        .car-meta {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }
        .footer {
            background-color: dimgray;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <img src="https://github.com/Lttt558/QHE4103-Coursework-Delivery-1/blob/feature-homepage/LOGO.jpg?raw=true" class="logo-img">
    <nav>
        <div class="links">
            <a href="Home.html">HOME</a>
            <a href="search_vehicle.php" class="active">SEARCH</a>
            <a href="add_vehicle.php">SELL</a>
            <a href="Login.html">LOGIN</a>
            <a href="Register.html">REGISTER</a>
        </div>
    </nav>

    <div class="search-container">
        <form method="get" class="search-bar">
            <input type="text" name="model" placeholder="Car model..." value="<?php echo htmlspecialchars($model); ?>">
            <input type="text" name="year" placeholder="Year..." value="<?php echo htmlspecialchars($year); ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>

        <div class="car-grid">
            <?php while ($car = mysqli_fetch_assoc($result)): ?>
            <div class="car-card">
                <img class="car-img" src="https://via.placeholder.com/400x225?text=<?php echo urlencode($car['car_model']); ?>">
                <div class="car-info">
                    <div class="car-title"><?php echo $car['car_model']; ?></div>
                    <div class="car-price">RMB<?php echo number_format($car['price']); ?></div>
                    <div class="car-meta">
                        <span><?php echo $car['year']; ?></span>
                        <span><?php echo $car['color']; ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($result) == 0): ?>
                <p style="grid-column: 1/-1; text-align:center; padding:30px;">No cars found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        © 2026 CarNext Car Trading System | All Rights Reserved
    </div>
</body>
</html>
