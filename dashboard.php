<?php

session_start();

require_once "db.php";

/* =====================================================
   LOGIN CHECK
===================================================== */

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}


/* =====================================================
   USER INFORMATION
===================================================== */

$username = $_SESSION['username'] ?? 'User';
$fullName = $_SESSION['full_name'] ?? 'System Administrator';
$role     = $_SESSION['role'] ?? 'User';


/* =====================================================
   HELPER FUNCTION
===================================================== */

function getCount($conn, $query)
{
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return (int)($row['total'] ?? 0);
}


function getAmount($conn, $query)
{
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return (float)($row['total'] ?? 0);
}


/* =====================================================
   DASHBOARD STATISTICS
===================================================== */

// Total Products
$totalProducts = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);


// Total Suppliers
$totalSuppliers = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM suppliers"
);


// Total Users
$totalUsers = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);


// Total Sales
$totalSales = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM sales"
);


// Low Stock
$totalLowStock = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM products
     WHERE quantity BETWEEN 1 AND 5"
);


// Out of Stock
$outStock = getCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM products
     WHERE quantity = 0"
);


// Inventory Value
$totalValue = getAmount(
    $conn,
    "SELECT IFNULL(SUM(price * quantity), 0) AS total
     FROM products"
);


// Total Revenue
$totalRevenue = getAmount(
    $conn,
    "SELECT IFNULL(SUM(total_price), 0) AS total
     FROM sales"
);


// Today's Revenue
$todayRevenue = getAmount(
    $conn,
    "SELECT IFNULL(SUM(total_price), 0) AS total
     FROM sales
     WHERE DATE(sale_date) = CURDATE()"
);


// This Month's Revenue
$monthRevenue = getAmount(
    $conn,
    "SELECT IFNULL(SUM(total_price), 0) AS total
     FROM sales
     WHERE YEAR(sale_date) = YEAR(CURDATE())
     AND MONTH(sale_date) = MONTH(CURDATE())"
);


/* =====================================================
   MONTHLY SALES DATA - LAST 12 MONTHS
===================================================== */

$monthlyLabels = [];
$monthlySales = [];

for ($i = 11; $i >= 0; $i--) {

    $date = new DateTime("first day of -" . $i . " month");

    $monthKey = $date->format("Y-m");

    $monthlyLabels[] = $date->format("M Y");

    $monthlySales[$monthKey] = 0;
}


// Get actual sales
$result = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(sale_date, '%Y-%m') AS month_key,
        SUM(total_price) AS revenue
     FROM sales
     WHERE sale_date >= DATE_FORMAT(
        DATE_SUB(CURDATE(), INTERVAL 11 MONTH),
        '%Y-%m-01'
     )
     GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
     ORDER BY month_key ASC"
);


if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        if (isset($monthlySales[$row['month_key']])) {

            $monthlySales[$row['month_key']] =
                (float)$row['revenue'];
        }
    }
}


$monthlySalesValues = array_values($monthlySales);


/* =====================================================
   STOCK STATUS
===================================================== */

/*
   Available = products with quantity greater than 5

   Low Stock = quantity 1 to 5

   Out of Stock = quantity 0
*/

$availableStock = $totalProducts - $totalLowStock - $outStock;

if ($availableStock < 0) {
    $availableStock = 0;
}


/* =====================================================
   TOP 5 BEST-SELLING PRODUCTS
===================================================== */

$topProducts = [];

$result = mysqli_query(
    $conn,
    "SELECT
        p.name,
        SUM(s.quantity) AS units_sold
     FROM sales s
     INNER JOIN products p
        ON s.product_id = p.product_id
     GROUP BY s.product_id, p.name
     ORDER BY units_sold DESC
     LIMIT 5"
);


if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $topProducts[] = $row;
    }
}


/* =====================================================
   LOW STOCK PRODUCTS
===================================================== */

$lowStockProducts = [];

$result = mysqli_query(
    $conn,
    "SELECT
        product_id,
        name,
        quantity
     FROM products
     WHERE quantity <= 5
     ORDER BY quantity ASC
     LIMIT 5"
);


if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $lowStockProducts[] = $row;
    }
}


/* =====================================================
   RECENT SALES
===================================================== */

$recentSales = [];

$result = mysqli_query(
    $conn,
    "SELECT
        s.sale_id,
        p.name,
        s.quantity,
        s.total_price,
        s.sale_date
     FROM sales s
     INNER JOIN products p
        ON s.product_id = p.product_id
     ORDER BY s.sale_date DESC
     LIMIT 5"
);


if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $recentSales[] = $row;
    }
}


/* =====================================================
   HEADER
===================================================== */

include "includes/header.php";

?>

<!-- =====================================================
     DASHBOARD
===================================================== -->

<div class="dashboard">


    <!-- =================================================
         WELCOME PANEL
    ================================================== -->

    <section class="welcome-panel">

        <div class="welcome-content">

            <div class="welcome-label">
                INVENTORY MANAGEMENT
            </div>

            <h1>
                Welcome back,
                <?= htmlspecialchars($fullName); ?> 👋
            </h1>

            <p>
                Here's what's happening with your inventory today.
            </p>

            <div class="user-info">

                <span>
                    👤
                    <strong>
                        <?= htmlspecialchars($fullName); ?>
                    </strong>
                </span>

                <span>
                    🎯
                    <?= htmlspecialchars($role); ?>
                </span>

            </div>

        </div>


        <!-- LIVE CLOCK -->

        <div class="live-clock">

            <div class="clock-icon">
                🕒
            </div>

            <div>

                <div id="liveDate">
                    Loading date...
                </div>

                <div id="liveTime">
                    Loading time...
                </div>

            </div>

        </div>

    </section>


    <!-- =================================================
         STATISTIC CARDS
    ================================================== -->

    <section class="stats-grid">


        <!-- PRODUCTS -->

        <a href="products/view.php"
           class="stat-card stat-blue">

            <div class="stat-icon">
                📦
            </div>

            <div class="stat-info">

                <span>
                    Total Products
                </span>

                <strong>
                    <?= number_format($totalProducts); ?>
                </strong>

            </div>

        </a>


        <!-- SALES -->

        <a href="sales/view.php"
           class="stat-card stat-green">

            <div class="stat-icon">
                💰
            </div>

            <div class="stat-info">

                <span>
                    Total Sales
                </span>

                <strong>
                    <?= number_format($totalSales); ?>
                </strong>

            </div>

        </a>


        <!-- SUPPLIERS -->

        <a href="suppliers/view.php"
           class="stat-card stat-purple">

            <div class="stat-icon">
                🚚
            </div>

            <div class="stat-info">

                <span>
                    Suppliers
                </span>

                <strong>
                    <?= number_format($totalSuppliers); ?>
                </strong>

            </div>

        </a>


        <!-- USERS -->

        <a href="#users"
           class="stat-card stat-cyan">

            <div class="stat-icon">
                👥
            </div>

            <div class="stat-info">

                <span>
                    Users
                </span>

                <strong>
                    <?= number_format($totalUsers); ?>
                </strong>

            </div>

        </a>


        <!-- LOW STOCK -->

        <a href="products/view.php"
           class="stat-card stat-orange">

            <div class="stat-icon">
                ⚠️
            </div>

            <div class="stat-info">

                <span>
                    Low Stock
                </span>

                <strong>
                    <?= number_format($totalLowStock); ?>
                </strong>

            </div>

        </a>


        <!-- OUT OF STOCK -->

        <a href="products/view.php"
           class="stat-card stat-red">

            <div class="stat-icon">
                🚨
            </div>

            <div class="stat-info">

                <span>
                    Out of Stock
                </span>

                <strong>
                    <?= number_format($outStock); ?>
                </strong>

            </div>

        </a>


        <!-- INVENTORY VALUE -->

        <div class="stat-card stat-dark">

            <div class="stat-icon">
                💎
            </div>

            <div class="stat-info">

                <span>
                    Inventory Value
                </span>

                <strong>
                    $<?= number_format($totalValue, 2); ?>
                </strong>

            </div>

        </div>


        <!-- TOTAL REVENUE -->

        <div class="stat-card stat-teal">

            <div class="stat-icon">
                📈
            </div>

            <div class="stat-info">

                <span>
                    Total Revenue
                </span>

                <strong>
                    $<?= number_format($totalRevenue, 2); ?>
                </strong>

            </div>

        </div>

    </section>


    <!-- =================================================
         REVENUE SUMMARY
    ================================================== -->

    <section class="revenue-grid">


        <!-- TODAY -->

        <div class="revenue-card">

            <span>
                Today's Revenue
            </span>

            <strong>
                $<?= number_format($todayRevenue, 2); ?>
            </strong>

            <small>
                Revenue generated today
            </small>

        </div>


        <!-- MONTH -->

        <div class="revenue-card">

            <span>
                This Month
            </span>

            <strong>
                $<?= number_format($monthRevenue, 2); ?>
            </strong>

            <small>
                Current month's revenue
            </small>

        </div>


        <!-- TOTAL -->

        <div class="revenue-card">

            <span>
                Total Revenue
            </span>

            <strong>
                $<?= number_format($totalRevenue, 2); ?>
            </strong>

            <small>
                All recorded sales
            </small>

        </div>

    </section>


    <!-- =================================================
         QUICK ACTIONS
    ================================================== -->

    <section class="dashboard-section">

        <div class="section-heading">

            <div>

                <span class="section-icon">
                    ⚡
                </span>

                <div>

                    <h2>
                        Quick Actions
                    </h2>

                    <p>
                        Manage your inventory quickly.
                    </p>

                </div>

            </div>

        </div>


        <div class="quick-actions">


            <a href="products/add.php"
               class="action-btn">

                <span>➕</span>
                Add Product

            </a>


            <a href="products/view.php"
               class="action-btn">

                <span>📦</span>
                Products

            </a>


            <a href="suppliers/add.php"
               class="action-btn">

                <span>🚚</span>
                Add Supplier

            </a>


            <a href="suppliers/view.php"
               class="action-btn">

                <span>🏢</span>
                Suppliers

            </a>


            <a href="sales/add.php"
               class="action-btn">

                <span>💰</span>
                New Sale

            </a>


            <a href="sales/view.php"
               class="action-btn">

                <span>📊</span>
                Sales

            </a>

        </div>

    </section>


    <!-- =================================================
         CHARTS
    ================================================== -->

    <section class="charts-grid">


        <!-- SALES CHART -->

        <div class="dashboard-panel sales-panel">

            <div class="panel-header">

                <div>

                    <h2>
                        📈 Sales Overview
                    </h2>

                    <p>
                        Revenue performance over the last 12 months
                    </p>

                </div>

                <span class="panel-badge">
                    12 Months
                </span>

            </div>

            <div class="chart-wrapper">

                <canvas id="salesChart"></canvas>

            </div>

        </div>


        <!-- STOCK CHART -->

        <div class="dashboard-panel stock-panel">

            <div class="panel-header">

                <div>

                    <h2>
                        🥧 Stock Status
                    </h2>

                    <p>
                        Current inventory condition
                    </p>

                </div>

            </div>

            <div class="stock-chart-wrapper">

                <canvas id="stockChart"></canvas>

            </div>

        </div>

    </section>


    <!-- =================================================
         TOP PRODUCTS + LOW STOCK
    ================================================== -->

    <section class="two-column-grid">


        <!-- TOP PRODUCTS -->

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>

                    <h2>
                        🔥 Top 5 Best-Selling Products
                    </h2>

                    <p>
                        Products with the highest sales volume
                    </p>

                </div>

                <a href="sales/view.php">
                    View All →
                </a>

            </div>


            <div class="ranking-list">

                <?php if (count($topProducts) > 0): ?>

                    <?php foreach ($topProducts as $index => $product): ?>

                        <div class="ranking-item">

                            <div class="rank-number">
                                <?= $index + 1; ?>
                            </div>


                            <div class="rank-product">

                                <strong>
                                    <?= htmlspecialchars($product['name']); ?>
                                </strong>

                                <span>
                                    <?= number_format((int)$product['units_sold']); ?>
                                    units sold
                                </span>

                            </div>


                            <div class="rank-icon">

                                <?php

                                if ($index === 0) {

                                    echo "🥇";

                                } elseif ($index === 1) {

                                    echo "🥈";

                                } elseif ($index === 2) {

                                    echo "🥉";

                                } else {

                                    echo "🏆";
                                }

                                ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-state">
                        No sales data available yet.
                    </div>

                <?php endif; ?>

            </div>

        </div>


        <!-- LOW STOCK -->

        <div class="dashboard-panel">

            <div class="panel-header">

                <div>

                    <h2>
                        ⚠️ Low Stock Alerts
                    </h2>

                    <p>
                        Products that need attention
                    </p>

                </div>

                <a href="products/view.php">
                    View All →
                </a>

            </div>


            <div class="alert-list">

                <?php if (count($lowStockProducts) > 0): ?>

                    <?php foreach ($lowStockProducts as $product): ?>

                        <a
                            href="products/edit.php?id=<?= (int)$product['product_id']; ?>"
                            class="stock-alert"
                        >

                            <div class="alert-icon">
                                ⚠️
                            </div>


                            <div class="alert-content">

                                <strong>
                                    <?= htmlspecialchars($product['name']); ?>
                                </strong>

                                <span>
                                    Only
                                    <b>
                                        <?= (int)$product['quantity']; ?>
                                    </b>
                                    units remaining
                                </span>

                            </div>


                            <div class="stock-badge">

                                <?= (int)$product['quantity']; ?>

                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="success-stock">

                        ✅
                        All products have sufficient stock.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =================================================
         RECENT SALES
    ================================================== -->

    <section class="dashboard-panel recent-sales">

        <div class="panel-header">

            <div>

                <h2>
                    🕒 Recent Sales
                </h2>

                <p>
                    Your latest transactions
                </p>

            </div>

            <a href="sales/view.php">
                View All →
            </a>

        </div>


        <div class="table-wrapper">

            <table class="dashboard-table">

                <thead>

                    <tr>

                        <th>
                            Sale ID
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Quantity
                        </th>

                        <th>
                            Total Price
                        </th>

                        <th>
                            Date
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (count($recentSales) > 0): ?>

                        <?php foreach ($recentSales as $sale): ?>

                            <tr>

                                <td>
                                    #<?= (int)$sale['sale_id']; ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($sale['name']); ?>
                                </td>


                                <td>

                                    <span class="quantity-badge">

                                        <?= (int)$sale['quantity']; ?>

                                    </span>

                                </td>


                                <td class="price-cell">

                                    $<?= number_format(
                                        (float)$sale['total_price'],
                                        2
                                    ); ?>

                                </td>


                                <td>

                                    <?= date(
                                        "d M Y, h:i A",
                                        strtotime($sale['sale_date'])
                                    ); ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5">

                                <div class="empty-state">
                                    No sales found.
                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>


<!-- =====================================================
     CHART.JS
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

/* =====================================================
   LIVE DATE & TIME
===================================================== */

function updateClock() {

    const now = new Date();


    const dateOptions = {

        weekday: "long",

        year: "numeric",

        month: "long",

        day: "numeric"

    };


    const timeOptions = {

        hour: "2-digit",

        minute: "2-digit",

        second: "2-digit"

    };


    const dateElement =
        document.getElementById("liveDate");


    const timeElement =
        document.getElementById("liveTime");


    if (dateElement) {

        dateElement.textContent =
            now.toLocaleDateString(
                "en-US",
                dateOptions
            );
    }


    if (timeElement) {

        timeElement.textContent =
            now.toLocaleTimeString(
                "en-US",
                timeOptions
            );
    }

}


updateClock();

setInterval(updateClock, 1000);


/* =====================================================
   SALES CHART
===================================================== */

const salesLabels =
    <?= json_encode($monthlyLabels); ?>;


const salesData =
    <?= json_encode($monthlySalesValues); ?>;


const salesCanvas =
    document.getElementById("salesChart");


if (salesCanvas) {

    const salesCtx =
        salesCanvas.getContext("2d");


    new Chart(
        salesCtx,
        {

            type: "line",


            data: {

                labels: salesLabels,


                datasets: [

                    {

                        label: "Revenue",

                        data: salesData,

                        borderWidth: 3,

                        fill: true,

                        tension: 0.4,

                        pointRadius: 4,

                        pointHoverRadius: 7

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,


                interaction: {

                    intersect: false,

                    mode: "index"

                },


                plugins: {

                    legend: {

                        display: false

                    },


                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return " Revenue: $" +
                                    Number(
                                        context.raw
                                    ).toLocaleString(
                                        "en-US",
                                        {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }
                                    );

                            }

                        }

                    }

                },


                scales: {

                    y: {

                        beginAtZero: true,


                        ticks: {

                            callback: function(value) {

                                return "$" +
                                    Number(value)
                                    .toLocaleString();

                            }

                        }

                    }

                }

            }

        }
    );

}


/* =====================================================
   STOCK STATUS CHART
===================================================== */

const stockCanvas =
    document.getElementById("stockChart");


if (stockCanvas) {

    const stockCtx =
        stockCanvas.getContext("2d");


    new Chart(
        stockCtx,
        {

            type: "doughnut",


            data: {

                labels: [

                    "Available",

                    "Low Stock",

                    "Out of Stock"

                ],


                datasets: [

                    {

                        data: [

                            <?= (int)$availableStock; ?>,

                            <?= (int)$totalLowStock; ?>,

                            <?= (int)$outStock; ?>

                        ],

                        borderWidth: 3,

                        hoverOffset: 10

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: "68%",


                plugins: {

                    legend: {

                        position: "bottom",


                        labels: {

                            padding: 20,

                            usePointStyle: true

                        }

                    }

                }

            }

        }
    );

}

</script>


<?php

include "includes/footer.php";

?>