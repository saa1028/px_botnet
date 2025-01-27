<?php
# Necessary at the top of every page for session management
session_start();

# If the RAT user isn't authenticated
if (!isset($_SESSION["authenticated"])) {
    # Redirects them to 403.php page
    header("Location: 403.php");
}
# Else they are authenticated
else {
    # Includes the RAT configuration file
    include "config/config.php";

    # Establishes a connection to the RAT database
    # Uses variables from "config/config.php"
    # "SET NAMES utf8" is necessary to be Unicode-friendly
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

if (isset($_POST["submit"])) {
    $username = $_POST["username"];     # New username
    $password = $_POST["password"];   # Password

    # When the user clicks "Add User"
    if (isset($_POST["submit"])) {
        # If all of the fields have been filled out
        if (isset($username) && !empty($username) && isset($password) && !empty($password)) {
            # Determines if the user account already exists in the "users" table
            $statement = $dbConnection->prepare("SELECT * FROM users WHERE username = :username");
            $statement->bindValue(":username", $username);
            $statement->execute();
            $rowCount = $statement->rowCount();

            # Kills database connection
            $statement->connection = null;

            # If the user account already exists
            # rowCount will be 1 if the user account already exists
            if ($rowCount == 1) {
                # Displays error message - "User already exists"
                echo "<br><div class='alert alert-danger'>User already exists.</div>";
            }
            # Else the user account does not exist
            else {
                # Adds new user to the "users" table
                $statement = $dbConnection->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $statement->bindValue(":username", $username);


                $password = password_hash($password, PASSWORD_BCRYPT);

                $statement->bindValue(":password", $password);
                $statement->execute();

                # Kills database connection
                $statement->connection = null;

                # Displays success message - "Successfully added new user. Redirecting back to index.php in 5 seconds. Do not refresh the page."
                echo "<br><div class='alert alert-success'>Successfully added new user. Redirecting back to index.php in 5 seconds. Do not refresh the page.</div>";

                # Waits 5 seconds, then redirects to submit.php
                # This is a hack to clear out the POST data
                header('Refresh: 5; URL=account.php');
            }
            # Else the passwords do not match
        }
        # Else they are missing fields
        else {
            # Displays error message - "Please fill out all fields."
            echo "<br><div class='alert alert-danger'>Please fill out all fields.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>P - X .Net</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="assets/css/untitled-1.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
    <script src="assets/js/fulkter.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0" style="background: #23272a;">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="index.php">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-network-wired"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>P - X .Net</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="nav navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i><span>&nbsp; Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="hosts.php"><i class="fas fa-user"></i><span>&nbsp; Bot</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="tasks.php"><i class="fas fa-table"></i><span>&nbsp; Task</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="output.php"><i class="fas fa-exclamation-circle"></i><span>&nbsp; Log</span></a></li>
                    <li class="nav-item"><a class="nav-link active" href="account.php"><i class="fas fa-window-maximize"></i><span>&nbsp; Account Management</span></a><a class="nav-link" href="network.php"><i class="fa fa-eercast"></i><span>&nbsp; Network Management</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper" style="background: #2c2f33;">
            <div id="content-fluid">
                <nav class="navbar navbar-expand shadow topbar static-top" style="background: #2c2f33;">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <form class="form-inline d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                            <div class="input-group">
                                <div class="input-group-append"></div>
                            </div>
                        </form>
                        <ul class="nav navbar-nav flex-nowrap ml-auto">
                            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#"><i class="fas fa-search"></i></a>
                                <div class="dropdown-menu dropdown-menu-right p-3 animated--grow-in" aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto navbar-search w-100">
                                        <div class="input-group"><input class="bg-light form-control border-0 small" type="text" placeholder="Search for ...">
                                            <div class="input-group-append"><button class="btn btn-primary py-0" type="button"><i class="fas fa-search"></i></button></div>
                                        </div>
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item dropdown no-arrow mx-1">
                                <div class="shadow dropdown-list dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown"></div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#"><span class="d-none d-lg-inline mr-2 text-gray-600 small"><?php echo $_SESSION["username"] ?></span></a>
                                    <div class="dropdown-menu shadow dropdown-menu-right animated--grow-in"><a class="dropdown-item" href="network-settings.php"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Settings</a><a class="dropdown-item" href="histo.php"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Activity log</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-light mb-4">Account Management</h3>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Accounts</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9 text-nowrap">
                                    <?php
                                    $statement = $dbConnection->prepare("SELECT * FROM hosts where status = 'connected'");
                                    $statement->execute();

                                    $bot = $statement->rowCount();
                                    $statement = $dbConnection->prepare("SELECT * FROM hosts");
                                    $statement->execute();

                                    $botoffline = $statement->rowCount();
                                    ?>
                                    <p id="botconnected">Connected: <?php echo $bot, "/", $botoffline; ?></p>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-md-right dataTables_filter" id="dataTable_filter"><label><input type="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search" name="search_input" id="search_input" onkeyup="fulkter()"></label></div>
                                </div>
                            </div>
                            <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                                <table class="table my-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">
                                        <?php
                                        # Gets a list of all of tasks
                                        # This information will be used to build a HTML table
                                        $statement = $dbConnection->prepare("SELECT * FROM users");
                                        $statement->execute();
                                        $results = $statement->fetchAll();

                                        # Kills database connection
                                        $statement->connection = null;

                                        # Builds HTML table for each host in the "tasks" table
                                        foreach ($results as $row) {
                                            echo "<tr>"; # Start of HTML table row
                                            echo "<td class='hostname'>" . $row["username"] . "</td>";
                                            echo "</tr>"; # End of HTML table row
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><strong>Username</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="row">


                                <div class="container">
                                    <!-- Start of main body container -->
                                    <form role="form" class="form-inline" method="post" action="">
                                        <!-- Start of task command form -->
                                        <input type="text" style="width: 400px;" class="form-control" name="username" placeholder="Username">&nbsp;
                                        <input type="password" style="width: 400px;" class="form-control" name="password" placeholder="Password">
                                        <button type="submit" name="submit" class="btn btn-default">Submit</button>
                                    </form> <!-- End of task command form -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <footer class="sticky-footer" style="background: #2c2f33;">
                    <div class="container my-auto">
                        <div class="text-center my-auto copyright"><span>Copyright © Brand 2022</span></div>
                    </div>
                </footer>
            </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/chart.min.js"></script>
        <script src="assets/js/bs-init.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
        <script src="assets/js/theme.js"></script>
</body>
</form>

</html>