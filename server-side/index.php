<?php
session_start();

header("Access-Control-Allow-Origin: http://eventpro.local");

require __DIR__ . '/halls.php';
require __DIR__ . '/event.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';
require __DIR__ . '/dashboard.php';
require __DIR__ . '/payments.php';

//  require __DIR__ . '/create_table.php';
//  createAllTables();

$current_page = explode("?", $_SERVER['REQUEST_URI'])[0];
$search_params = explode("?", $_SERVER['REQUEST_URI'])[1] ?? null;
$expodeURL = explode('/', $current_page);
$section = explode('/', $current_page)[1] ?? null;
$function = explode('/', $current_page)[2] ?? null;
$subFunctions = explode('/', $current_page)[3] ?? null;
$eventSection = htmlspecialchars($_GET['eventsection'] ?? 'booked-events') ?? null;

// echo($current_page);
// echo($search_params);

if($function === 'api'){
    switch ($_SERVER["REQUEST_METHOD"]){
        case "POST":
            if($section === 'form-submit'){
                try{
                    $client_full_name = htmlspecialchars(trim($_POST['client_full_name']));
                    $client_phone_number = htmlspecialchars(trim($_POST['client_phone_number']));
                    $client_email = htmlspecialchars(trim($_POST['client_email']));
                    $client_address = htmlspecialchars(trim($_POST['client_address']));

                    $client_id = registerNewClientFromEvents($client_full_name, $client_phone_number, $client_email, $client_address);

                    $select_event_id = htmlspecialchars(trim($_POST['select-event']));
                    $select_event_name = htmlspecialchars(trim($_POST['event_name']));
                    $event_date = htmlspecialchars(trim($_POST['event_date']));
                    $num_participants = htmlspecialchars(trim($_POST['num_participants']));
                    $event_location = htmlspecialchars(trim($_POST['event_location']));
                    $hall_id = htmlspecialchars(trim($_POST['select-hall']));
                    $food_id = htmlspecialchars(trim($_POST['select-food']));
                    $beverages_id = htmlspecialchars(trim($_POST['select-beverage']));
                    $details = htmlspecialchars(trim($_POST['details']));

                    if($select_event_id == "" || $select_event_id == null){
                        $result = createClientCustomeEvent($client_id, $select_event_name, $event_date, $num_participants, $event_location, $hall_id, $food_id, $beverages_id, $details);
                    } else {
                        $result = createClientEvent($client_id, $select_event_id, $event_date, $num_participants);
                    }

                    $message = [
                        'client_full_name' => $client_full_name,
                        'client_phone_number' => $client_phone_number,
                        'client_email' => $client_email,
                        'client_address' => $client_address,
                        'select_event_id' => $select_event_id,
                        'select_event_name' => $select_event_name,
                        'event_date' => $event_date,
                        'num_participants' => $num_participants,
                        'event_location' => $event_location,
                        'hall_id' => $hall_id,
                        'food_id' => $food_id,
                        'beverages_id' => $beverages_id,
                    ];
                
                    // Convert the array to a JSON string for readability in the log
                    error_log(json_encode($message, JSON_PRETTY_PRINT), 3, __DIR__ . "/error.log");
                    error_log($result, 3, __DIR__ . "/error.log");
                    if(!$result){
                        throw new Exception("Creating Event Faild.");
                    }
                    header('Location: http://eventpro.local/book_event_success.html');
                } catch (Exception $e) {
                    header('Location: http://eventpro.local/book_event_error.html');
                }
                exit();
            }
            break;
        case "GET":
            if (isset($_SESSION['adminname'])) {
                if ($section === 'event') {
                    if ($subFunctions === 'getevent') {
                        if ($expodeURL[4]) {
                            $event_id = $expodeURL[4];
                            echo json_encode(['event' => getEventById($event_id)]);
                            http_response_code(200);
                            exit();
                        } else {
                            http_response_code(401);
                        }
                    } elseif ($subFunctions === 'getfoods') {
                        if ($expodeURL[4]) {
                            $food_id = $expodeURL[4];
                            echo json_encode(['food' => getFoodsById($food_id)]);
                            http_response_code(200);
                            exit();
                        } else {
                            http_response_code(401);
                        }
                    } elseif ($subFunctions === 'getbeverages') {
                        if ($expodeURL[4]) {
                            $beverage_id = $expodeURL[4];
                            echo json_encode(['beverage' => getBeveragesById($beverage_id)]);
                            http_response_code(200);
                            exit();
                        } else {
                            http_response_code(401);
                        }
                    }
                } elseif ($section === 'halls') {
                    if ($subFunctions === 'gethall') {
                        if ($expodeURL[4]) {
                            $hall_id = $expodeURL[4];
                            echo json_encode(['hall' => getHallById($hall_id)]);
                            http_response_code(200);
                            exit();
                        } else {
                            http_response_code(401);
                        }
                    }
                } elseif ($section === 'payments') {
                    if ($subFunctions === 'getpayment') {
                        if ($expodeURL[4]) {
                            $payment_id = $expodeURL[4];
                            echo json_encode(['payments' => getPaymentById($payment_id)]);
                            http_response_code(200);
                            exit();
                        } else {
                            http_response_code(401);
                        }
                    }
                }
            } else {
                if ($section === 'event') {
                    if ($subFunctions === 'getallevents') {
                        echo json_encode(['events' => getAllEventsByFiltering()]);
                        http_response_code(200);
                        exit();
                    } elseif ($subFunctions === 'getallfoods') {
                        echo json_encode(['foods' => getAllFoodsByFiltering()]);
                        http_response_code(200);
                        exit();
                    } elseif ($subFunctions === 'getallbeverages') {
                        echo json_encode(['beverages' => getAllBeveragesByFiltering()]);
                        http_response_code(200);
                        exit();
                    }
                } elseif ($section === 'halls') {
                    if ($subFunctions === 'getallhalls') {
                        echo json_encode(['halls' => getAllHallsByFiltering()]);
                        http_response_code(200);
                        exit();
                    }
                }
            }
            break;
    }
    http_response_code(404);
    exit();
}


switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        if ($section === 'login') {
            if ($function === 'adminlogin') {
                $adminname = htmlspecialchars(trim($_POST['adminname']));
                $password = htmlspecialchars(trim($_POST['password']));
                $result = adminLogin($adminname, $password);
                echo ($result);
                header('Location: /');
                exit();
            }
        }
        if (isset($_SESSION['adminname'])) {
            if($section === 'halls') {
                if ($function === 'createnew') {
                    $number = htmlspecialchars(trim($_POST['number']));
                    $name = htmlspecialchars(trim($_POST['name']));
                    $number_of_tables = htmlspecialchars(trim($_POST['number_of_tables']));
                    $capacity = htmlspecialchars(trim($_POST['capacity']));
                    $details = htmlspecialchars(trim($_POST['details']));
                    $result = createNewHall($number, $name, $number_of_tables, $capacity, $details);
                    echo ($result);
                    header('Location: /halls');
                    exit();
                } elseif ($function === 'update') {
                    if($expodeURL[3]){
                        $hall_id = $expodeURL[3];
                        $number = htmlspecialchars(trim($_POST['number']));
                        $name = htmlspecialchars(trim($_POST['name']));
                        $number_of_tables = htmlspecialchars(trim($_POST['number_of_tables']));
                        $capacity = htmlspecialchars(trim($_POST['capacity']));
                        $details = htmlspecialchars(trim($_POST['details']));
                        $result = updateNewHall($hall_id, $number, $name, $number_of_tables, $capacity, $details);
                        echo ($result);
                    }
                    header('Location: /halls');
                    exit();
                }
            } elseif ($section === 'event') {
                if ($function === 'createnew') {
                    $event_name = htmlspecialchars(trim($_POST['event_name']));
                    $location = htmlspecialchars(trim($_POST['location']));
                    $is_confirm = isset($_POST['is_confirm']) ? 1 : 0;
                    $status = htmlspecialchars(trim($_POST['status']));
                    $details = htmlspecialchars(trim($_POST['details']));
                    $hall_id = htmlspecialchars(trim($_POST['hall_id']));
                    $food_id = htmlspecialchars(trim($_POST['food_id']));
                    $beverages_id = htmlspecialchars(trim($_POST['beverages_id']));
                    $event_fee = htmlspecialchars(trim($_POST['event_fee']));

                    $event_image = isset($_FILES['event_image_url']) ? $_FILES['event_image_url'] : null;

                    $result = createNewEvent(
                        $event_name,
                        $location,
                        $is_confirm,
                        $status,
                        $details,
                        $event_image,
                        $hall_id,
                        $food_id,
                        $beverages_id,
                        $event_fee
                    );

                    echo ($result);
                    header('Location: /event');
                    exit();
                } elseif ($function === 'createnewfood') {
                    $name = htmlspecialchars(trim($_POST['name']));
                    $pre_plate_price = htmlspecialchars(trim($_POST['per_plate_price']));
                    $max_plates = htmlspecialchars(trim($_POST['max_plates']));
                    $result = createNewFood($name, $pre_plate_price, $max_plates);
                    echo ($result);
                    header('Location: /event?eventsection=foods');
                    exit();
                } elseif ($function === 'createnewbeverage') {
                    $name = htmlspecialchars(trim($_POST['name']));
                    $per_glass_price = htmlspecialchars(trim($_POST['per_glass_price']));
                    $max_glass = htmlspecialchars(trim($_POST['max_glass']));
                    $max_bottle = htmlspecialchars(trim($_POST['max_bottle']));
                    $per_bottle_price = htmlspecialchars(trim($_POST['per_bottle_price']));
                    $result = createNewBeverage($name, $per_glass_price, $max_glass, $max_bottle, $per_bottle_price);
                    echo ($result);
                    header('Location: /event?eventsection=beverages');
                    exit();
                } elseif ($function === 'update') {
                    if($expodeURL[3]){
                        $event_id = $expodeURL[3];
                        $event_name = htmlspecialchars(trim($_POST['event_name']));
                        $location = htmlspecialchars(trim($_POST['location']));
                        $is_confirm = isset($_POST['is_confirm']) ? 1 : 0;
                        $status = htmlspecialchars(trim($_POST['status']));
                        $details = htmlspecialchars(trim($_POST['details']));
                        $hall_id = htmlspecialchars(trim($_POST['hall_id']));
                        $food_id = htmlspecialchars(trim($_POST['food_id']));
                        $beverages_id = htmlspecialchars(trim($_POST['beverages_id']));
                        $event_fee = htmlspecialchars(trim($_POST['event_fee']));

                        $event_image = isset($_FILES['event_image_url']) ? $_FILES['event_image_url'] : null;

                        $result = updateEvent(
                            $event_id,
                            $event_name,
                            $location,
                            $is_confirm,
                            $status,
                            $details,
                            $event_image,
                            $hall_id,
                            $food_id,
                            $beverages_id,
                            $event_fee
                        );

                        echo ($result);
                    }
                    header('Location: /event');
                    exit();
                } elseif ($function === 'foodupdate') {
                    if($expodeURL[3]){
                        $food_id = $expodeURL[3];
                        $name = htmlspecialchars(trim($_POST['name']));
                        $per_plate_price = htmlspecialchars(trim($_POST['per_plate_price']));
                        $max_plates = htmlspecialchars(trim($_POST['max_plates']));
                        $result = updateFood($food_id, $name, $per_plate_price, $max_plates);
                        echo ($result);
                    }
                    header('Location: /event?eventsection=foods');
                    exit();
                } elseif ($function === 'beveragesupdate') {
                    if($expodeURL[3]){
                        $beverage_id = $expodeURL[3];
                        $name = htmlspecialchars(trim($_POST['name']));
                        $per_glass_price = htmlspecialchars(trim($_POST['per_glass_price']));
                        $max_glass = htmlspecialchars(trim($_POST['max_glass']));
                        $max_bottle = htmlspecialchars(trim($_POST['max_bottle']));
                        $per_bottle_price = htmlspecialchars(trim($_POST['per_bottle_price']));
                        $result = updateBeverage($beverage_id, $name, $per_glass_price, $max_glass, $max_bottle, $per_bottle_price);
                        echo ($result);
                    }
                    header('Location: /event?eventsection=beverages');
                    exit();
                }
            } elseif ($section === 'clients') {
                if ($function === 'createnew') {
                    $fullName = htmlspecialchars(trim($_POST['full_name']));
                    $phoneNumber = htmlspecialchars(trim($_POST['phone_number']));
                    $email = htmlspecialchars(trim($_POST['email']));
                    $address = htmlspecialchars(trim($_POST['address']));
                    $result = createNewClient($fullName, $phoneNumber, $email, $address);
                    echo ($result);
                    header('Location: /clients');
                    exit();
                }

            } elseif($section === 'payments'){
                if ($function === 'createnew') {
                    $client_id = htmlspecialchars(trim($_POST['client_id']));
                    $amount = htmlspecialchars(trim($_POST['amount']));
                    $status = htmlspecialchars(trim($_POST['status']));

                    $result = createNewPayment( $amount, $status, $client_id);
                    echo ($result);
                    header('Location: /payments');
                    exit();
                } elseif ($function === 'update') {
                    if($expodeURL[3]){
                        $payment_id = $expodeURL[3];
                        $client_id = htmlspecialchars(trim($_POST['client_id']));
                        $amount = htmlspecialchars(trim($_POST['amount']));
                        $status = htmlspecialchars(trim($_POST['status']));
    
                        $result = updateNewPayment($payment_id, $amount, $status, $client_id);
                        echo ($result);
                    }
                    header('Location: /payments');
                    exit();
                }
            } else {
                header('Location: /404');
                exit();
            }
        } else {
            header('Location: /login');
            exit();
        }
        break;
    case "GET":
        if ($section === 'logout') {
            session_unset();
            session_destroy();
            header('Location: /login');
            exit();
        }
        break;
    case "DELETE":
        if (isset($_SESSION['adminname'])) {
            if ($section === 'halls') {
                if ($function === 'delete') {
                    if ($expodeURL[3]) {
                        $result = deleteHallById($expodeURL[3]);
                        echo ($result);
                    }
                    exit();
                }
            } elseif ($section === 'event') {
                if ($function === 'delete') {
                    if ($expodeURL[3]) {
                        $result = deleteEventById($expodeURL[3]);
                        echo ($result);
                    }
                    exit();
                } elseif ($function === 'statusunpaid') {
                    if ($expodeURL[3]) {
                        $result = changeStatusEventById($expodeURL[3], 'unpaid');
                        echo ($result);
                    }
                    exit();
                } elseif ($function === 'statuspaid') {
                    if ($expodeURL[3]) {
                        $result = changeStatusEventById($expodeURL[3], 'paid');
                        echo ($result);
                    }
                    exit();
                } elseif ($function === 'statusprocess') {
                    if ($expodeURL[3]) {
                        $result = changeStatusEventById($expodeURL[3], 'processing');
                        echo ($result);
                    }
                    exit();
                }
            }  elseif ($section === 'foods') {
                if ($function === 'delete') {
                    if ($expodeURL[3]) {
                        $result = deleteFoodsById($expodeURL[3]);
                        echo ($result);
                    }
                    exit();
                }
            }  elseif ($section === 'beverages') {
                if ($function === 'delete') {
                    if ($expodeURL[3]) {
                        $result = deleteBeveragesById($expodeURL[3]);
                        echo ($result);
                    }
                    exit();
                }  
            } else {
                header('Location: /404');
                exit();
            }
        } else {
            header('Location: /login');
            exit();
        }
        break;
    case "PUT":
        echo ("PUT FUNCTION WORKING!");
        break;
    default:
        echo ("UNKNOWN FUNCTION!");
        break;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Panel <?php if ($section != '/') { echo ("| $section");} ?></title>
</head>

<body>

    <?php if ($current_page === "/login" && !isset($_SESSION['adminname'])): ?>
        <!-- Login -->
        <div class="login-page">
            <div class="form">
                <h2 style="text-align: center; margin-bottom: 20px;">Admin login</h2>
                <form action="/login/adminlogin" method="post" enctype="multipart/form-data" class="login-form">
                    <input type="text" name="adminname" placeholder="adminname" />
                    <input type="password" name="password" placeholder="password" />
                    <button>login</button>
                </form>
            </div>
        </div>
        <!-- Login End -->
    <?php else:?>
    <?php if (!isset($_SESSION['adminname'])) {header('Location: /login'); exit();} ?>

    <div class="container">
        <!-- SideBar -->
        <div class="sidebar">
            <ul>
                <li>
                    <a href="/" style="justify-content: center;">
                        <div class="title"><span>Event</span> Pro</div>
                    </a>
                </li>
                <li <?= $current_page === "/" || $current_page === "/dashboard"? 'class="selected"' : ''?>>
                    <a href="/dashboard">
                        <i class="fas fa-th-large"></i>
                        <div class="title">Dashboard</div>
                    </a>
                </li>
                <li <?= $current_page === "/event"? 'class="selected"' : ''?>>
                    <a href="/event">
                        <i class="fa-brands fa-slack"></i>
                        <div class="title">Events</div>
                    </a>
                </li>
                <li <?= $current_page === "/promotions"? 'class="selected"' : ''?>>
                    <a href="/promotions">
                        <i class="fa-solid fa-circle-up"></i>
                        <div class="title">Promotion</div>
                    </a>
                </li>
                <li <?= $current_page === "/payments"? 'class="selected"' : ''?>>
                    <a href="/payments">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <div class="title">Payments</div>
                    </a>
                </li>
                <li <?= $current_page === "/halls"? 'class="selected"' : ''?>>
                    <a href="/halls">
                        <i class="fa-solid fa-place-of-worship"></i>
                        <div class="title">Halls</div>
                    </a>
                </li>
                <li <?= $current_page === "/services"? 'class="selected"' : ''?>>
                    <a href="/services">
                        <i class="fa-solid fa-bell-concierge"></i>
                        <div class="title">Services</div>
                    </a>
                </li>
                <li <?= $current_page === "/clients"? 'class="selected"' : ''?>>
                    <a href="/clients">
                    <i class="fa-solid fa-person"></i>
                        <div class="title">Clients</div>
                    </a>
                </li>
            </ul>
        </div>
        <!-- SideBar End -->

        <div class="main">
            <div class="top-bar">
                <div class="search">
                    <input type="text" name="search" placeholder="Search here">
                    <label for="search"><i class="fas fa-search"></i></label>
                </div>
                <i class="fas fa-bell"></i>
                <div class="user">
                    <img src="assets/person.jpg" alt="">
                </div>
                <a href="/logout">Logout</a>
            </div>

            <?php if ($current_page === "/" || $current_page === "/dashboard"):?>
                <!-- Dashboard -->
                <div class="cards">
                    <?php foreach(getCountsList() as $kaynam => $cardvalue): ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="number"><?= $cardvalue?></div>
                            <div class="card-name"><?= $kaynam?></div>
                            <div class="icon-box">
                                <?php if($kaynam === 'Booked Events'): ?>
                                <i class="fa-solid fa-pen-to-square"></i>
                                <?php elseif($kaynam === 'Events'): ?>
                                <i class="fa-solid fa-pen-to-square"></i>
                                <?php elseif($kaynam === 'Pending Payments'): ?>
                                <i class="fa-solid fa-money-check-dollar"></i>
                                <?php elseif($kaynam === 'Halls'): ?>
                                <i class="fa-solid fa-place-of-worship"></i>
                                <?php elseif($kaynam === 'Earnings'): ?>
                                <i class="fa-solid fa-money-check-dollar"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
                <div class="tables">
                    <div class="booked-events">
                        <div class="heading">
                            <h2>Booked Events</h2>
                            <a href="#" class="btn">View All</a>
                        </div>
                        <table class="appointments">
                            <thead>
                                <td>Name</td>
                                <td>Event</td>
                                <td>Hall</td>
                                <td>Actions</td>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Liam Smith Doe</td>
                                    <td>Wedding</td>
                                    <td>Wedding-01</td>
                                    <td>
                                        <i class="far fa-eye"></i>
                                        <i class="far fa-edit"></i>
                                        <i class="far fa-trash-alt"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="customer-review">
                        <div class="heading">
                            <h2>Customer Reviews</h2>
                            <a href="#" class="btn">View All</a>
                        </div>
                        <table class="visiting">
                            <thead>
                                <td>Photo</td>
                                <td>Name</td>
                                <td>Event</td>
                                <td>Detail</td>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="img-box-small">
                                            <img src="assets/person.jpg" alt="">
                                        </div>
                                    </td>
                                    <td>Vipu</td>
                                    <td>Seminar</td>
                                    <td><i class="far fa-eye"></i></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="img-box-small">
                                            <img src="assets/person.jpg" alt="">
                                        </div>
                                    </td>
                                    <td>Vipu</td>
                                    <td>Wedding</td>
                                    <td><i class="far fa-eye"></i></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="img-box-small">
                                            <img src="assets/person.jpg" alt="">
                                        </div>
                                    </td>
                                    <td>Vipu</td>
                                    <td>Party</td>
                                    <td><i class="far fa-eye"></i></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="img-box-small">
                                            <img src="assets/person.jpg" alt="">
                                        </div>
                                    </td>
                                    <td>Vipu</td>
                                    <td>Wedding</td>
                                    <td><i class="far fa-eye"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Dashboard End -->
            
            <?php elseif ($current_page === "/event"): ?>
                <!-- Event -->
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Events</h1> -->

                    <section class="header-container" style="justify-content: space-between;">
                        <div class="section-buttons">
                            <button id="btn-section-booked-events" <?= $eventSection === 'booked-events'? 'class="selected"' : '' ?> onclick="handleSectionChange(event, 'booked-events')">Booked Events</button>
                            <button id="btn-section-events" <?= $eventSection === 'events'? 'class="selected"' : '' ?> onclick="handleSectionChange(event, 'events')">Events</button>
                        </div>
                        <div class="section-buttons">
                            <button id="btn-section-booked-events" <?= $eventSection === 'foods'? 'class="selected"' : '' ?> onclick="handleSectionChange(event, 'foods')">Foods</button>
                            <button id="btn-section-events" <?= $eventSection === 'beverages'? 'class="selected"' : '' ?> onclick="handleSectionChange(event, 'beverages')">Beverages</button>
                        </div>
                        <script>
                            function handleSectionChange(event, section){
                                const baseURL = window.location.origin + window.location.pathname;
                                const params = new URLSearchParams(window.location.search);
                                params.set('eventsection', section);
                                window.location.replace(baseURL + "?" + params.toString());
                            }
                        </script>
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Event</button>
                            <button id="btn-add" class="btn add" onclick="handleOpenAddFormFood()">Add A New Food</button>
                            <button id="btn-add" class="btn add" onclick="handleOpenAddFormBeverage()">Add A New Beverage</button>
                        </div>
                    </section>
                    <hr />

                    <?php if($eventSection === 'booked-events' || $eventSection === 'events'): ?>
                    <section class="show">
                        <div class="show-container">
                            <?php if($eventSection): ?>
                            <?php
                                $eventsList = getAllEvents($eventSection);
                                if($eventsList):
                                foreach ($eventsList as $row): 
                            ?>
                                <!-- Event Card -->
                                <div class="event-card">
                                    <div style="position: absolute; z-index: 1; inset: 0; display: flex; flex-direction: column;">
                                        <div class="event-details" style="flex: 1;">
                                            <h3><?= $row['event_name'] ?></h3>
                                            <?php if($row['event_date']): ?>
                                            <p>Date: <?= $row['event_date'] ?></p>
                                            <?php endif; ?>
                                            <?php if($row['details']): ?>
                                            <p>Description: <?= $row['details'] ?></p>
                                            <?php endif; ?>
                                            <h4>LKR <?= number_format($row['event_fee'], 2) ?></h4>
                                            <p>Client: <?= $row['client_name'] ?> - <?= $row['client_phone'] ?></p>
                                        </div>
                                        <div class="event-actions">
                                            <div>
                                                <p><?= ucfirst($row['status']) ?></p>
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <?php if($row['status'] === 'processing'): ?>
                                                    <button data-event-id="<?= $row['id'] ?>" title="Pay" onclick="handleEventStatusPaid(event)"><i class="fa-solid fa-hand-holding-dollar"></i></button>
                                                <?php elseif($row['status'] === 'paid'): ?>
                                                    <button data-event-id="<?= $row['id'] ?>" title="Unpay" onclick="handleEventStatusUnpaid(event)"><i class="fa-regular fa-circle-xmark"></i></button>
                                                <?php elseif($row['status'] === 'unpaid'): ?>
                                                    <button data-event-id="<?= $row['id'] ?>" title="Processing" onclick="handleEventStatusProcessing(event)"><i class="fa-solid fa-spinner"></i></button>
                                                <?php endif; ?>
                                                <button data-event-id="<?= $row['id'] ?>" title="Edit" onclick="handleEventEdit(event)"><i class="fa fa-edit"></i></button>
                                                <button data-event-id="<?= $row['id'] ?>" title="Delete" onclick="handleEventDelete(event)"><i class="fa fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($row['event_image_url']): ?>
                                    <img src="<?= $row['event_image_url'] ?>" alt="Event 1" class="event-image" />
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <h3>NO EVENT FOUND.</h3>
                            <?php
                                endif;
                                endif;
                            ?>
                        </div>
                    </section>
                    <?php elseif($eventSection === 'foods'): ?>
                        <div class="table-content-section">
                            <h3>Foods Details</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Per Plate Price</th>
                                        <th>Max Plates</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (getAllFoods() as $row): ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $row['id'] ?></td>
                                            <td style="text-align: center;"><?= $row['name'] ?></td>
                                            <td style="text-align: center;"><?= $row['per_plate_price'] ?></td>
                                            <td style="text-align: center;"><?= $row['max_plates'] ?></td>
                                            <td>
                                                <div style="display: flex; gap: 10px; justify-content: center;">
                                                    <button data-foods-id="<?= $row['id'] ?>" onclick="handleFoodsEdit(event)"><i class="fa fa-edit"></i></button>
                                                    <button data-foods-id="<?= $row['id'] ?>" onclick="handleFoodsDelete(event)"><i class="fa fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif($eventSection === 'beverages'): ?>
                        <div class="table-content-section">
                            <h3>Beverages Details</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Per Glass Price</th>
                                        <th>Max Glass</th>
                                        <th>Max Bottle</th>
                                        <th>Per Bottle Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (getAllBeverages() as $row): ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $row['id'] ?></td>
                                            <td style="text-align: center;"><?= $row['name'] ?></td>
                                            <td style="text-align: center;"><?= $row['per_glass_price'] ?></td>
                                            <td style="text-align: center;"><?= $row['max_glass'] ?></td>
                                            <td style="text-align: center;"><?= $row['max_bottle'] ?></td>
                                            <td style="text-align: center;"><?= $row['per_bottle_price'] ?></td>
                                            <td>
                                                <div style="display: flex; gap: 10px; justify-content: center;">
                                                    <button data-beverages-id="<?= $row['id'] ?>" onclick="handleBeveragesEdit(event)"><i class="fa fa-edit"></i></button>
                                                    <button data-beverages-id="<?= $row['id'] ?>" onclick="handleBeveragesDelete(event)"><i class="fa fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <h3 style="text-align: center;">NO EVENT FOUND.</h3>
                    <?php endif; ?>
                    <script>
                        function handleEventDelete(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + window.location.pathname + '/delete/' + eventID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }

                        function handleEventPay(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + window.location.pathname + '/paidstatus/' + eventID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }

                        function handleFoodsDelete(event) {
                            const foodsID = event.currentTarget.getAttribute('data-foods-id');
                            const baseURL = window.location.origin + '/foods/delete/' + foodsID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }

                        function handleBeveragesDelete(event) {
                            const beveragesID = event.currentTarget.getAttribute('data-beverages-id');
                            const baseURL = window.location.origin + '/beverages/delete/' + beveragesID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }

                        function handleEventStatusPaid(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + '/event/statuspaid/' + eventID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }
                        function handleEventStatusUnpaid(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + '/event/statusunpaid/' + eventID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }
                        function handleEventStatusProcessing(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + '/event/statusprocess/' + eventID;
                            fetch(baseURL, {
                                method: "DELETE"
                            });
                            window.location.reload();
                        }

                        async function handleEventEdit(event) {
                            const eventID = event.currentTarget.getAttribute('data-event-id');
                            const baseURL = window.location.origin + window.location.pathname + '/api/getevent/' + eventID;
                            const editFormContainer = document.getElementById("edit-container-event");

                            editFormContainer.style.display = 'flex';
                            document.getElementById('add-btn-x-edit-event').addEventListener('click', function() {
                                this.parentElement.style.display = 'none';
                            });

                            const response = await fetch(baseURL, {
                                method: "GET",
                            });
                            if(response.ok){
                                const data = await response.json();
                                editFormContainer.getElementsByTagName('form')[0].action = `${window.location.origin}/event/update/${eventID}`;
                                const listOfInputs = { event_name: 'input', location: 'input', is_confirm: 'select', status: 'select', details: 'textarea', hall_id: 'select', food_id: 'select', beverages_id: 'select', event_fee: 'input'};
                                for(let keynam in listOfInputs){
                                    editFormContainer.querySelector(`${listOfInputs[keynam]}[name="${keynam}"]`).value = data['event'][`${keynam}`];
                                }
                            }
                        }

                        async function handleFoodsEdit(event) {
                            const foodID = event.currentTarget.getAttribute('data-foods-id');
                            const baseURL = window.location.origin + window.location.pathname + '/api/getfoods/' + foodID;
                            const editFormContainer = document.getElementById("edit-container-foods");

                            editFormContainer.style.display = 'flex';
                            document.getElementById('edit-btn-x-food').addEventListener('click', function() {
                                this.parentElement.style.display = 'none';
                            });

                            const response = await fetch(baseURL, {
                                method: "GET",
                            });
                            if(response.ok){
                                const data = await response.json();
                                editFormContainer.getElementsByTagName('form')[0].action = `${window.location.origin}/event/foodupdate/${foodID}`;
                                const listOfInputs = { name: 'input', per_plate_price: 'input', max_plates: 'input'};
                                for(let keynam in listOfInputs){
                                    editFormContainer.querySelector(`${listOfInputs[keynam]}[name="${keynam}"]`).value = data['food'][`${keynam}`];
                                }
                            }
                        }

                        async function handleBeveragesEdit(event) {
                            const beveragesID = event.currentTarget.getAttribute('data-beverages-id');
                            const baseURL = window.location.origin + window.location.pathname + '/api/getbeverages/' + beveragesID;
                            const editFormContainer = document.getElementById("edit-container-beverages");

                            editFormContainer.style.display = 'flex';
                            document.getElementById('edit-btn-x-beverages').addEventListener('click', function() {
                                this.parentElement.style.display = 'none';
                            });

                            const response = await fetch(baseURL, {
                                method: "GET",
                            });
                            if(response.ok){
                                const data = await response.json();
                                editFormContainer.getElementsByTagName('form')[0].action = `${window.location.origin}/event/beveragesupdate/${beveragesID}`;
                                const listOfInputs = { name: 'input', max_glass: 'input', per_glass_price: 'input', max_bottle: 'input', per_bottle_price: 'input'};
                                for(let keynam in listOfInputs){
                                    editFormContainer.querySelector(`${listOfInputs[keynam]}[name="${keynam}"]`).value = data['beverage'][`${keynam}`];
                                }
                            }
                        }
                    </script>

                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">
                            <form action="/event/createnew" method="post" enctype="multipart/form-data">
                                <h3>Add Event</h3>

                                <input type="text" name="event_name" placeholder="Enter Title of the Event" class="box" maxlength="50" required />

                                <input type="text" name="location" placeholder="Enter Location (Optional)" class="box" maxlength="255" required />

                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <label for="is_confirm">Is Confirmed:</label>
                                    <select name="is_confirm" id="is_confirm" class="box" required>
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <label for="status">Event Status:</label>
                                    <select name="status" id="status" class="box" required>
                                        <option value="unpaid" selected>Unpaid</option>
                                        <option value="processing">Processing</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>

                                <textarea name="details" placeholder="Details of the Event" class="box" maxlength="255" required></textarea>

                                <select name="hall_id" id="hall_id" class="box" required>
                                    <option value="">Select The Hall</option>
                                    <?php foreach (getAllHalls() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="food_id" id="food_id" class="box" placeholder="Select The Food">
                                    <option value="">Select The Food</option>
                                    <?php foreach (getAllFoods() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] . ' - Max: ' . $row['max_plates'] . ' - LKR ' . $row['per_plate_price'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="beverages_id" id="food_id" class="box" placeholder="Select The Beverages">
                                    <option value="">Select The Beverages</option>
                                    <?php foreach (getAllBeverages() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?><?php if ($row['max_glass'] != 0): ?> <?= '- Max Glass: ' . $row['max_glass'] ?><?php endif; ?> <?php if ($row['max_bottle'] != 0): ?> <?= '- Max Glass: ' . $row['max_bottle'] ?><?php endif; ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="file" name="event_image_url" accept=".jpg, .jpeg, .png" required class="box" />

                                <input type="number" step="0.01" name="event_fee" required placeholder="Enter Price For Event" class="box">

                                <button type="submit">Add Event</button>
                            </form>
                        </div>
                    </div>

                    <div class="add-container" id="add-container-food">
                        <button id="add-btn-x-food" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/event/createnewfood" method="post" enctype="multipart/form-data">
                                <h3>Add Food Now</h3>

                                <input type="text" name="name" required placeholder="Enter Food Name" class="box" maxlength="50">

                                <input type="number" step="0.01" name="per_plate_price" required placeholder="Enter Price per Plate" class="box">

                                <input type="text" name="max_plates" required placeholder="Enter Maximum Plates" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <button type="submit">Add Food</button>
                            </form>


                        </div>
                    </div>

                    <div class="add-container" id="add-container-beverage">
                        <button id="add-btn-x-beverage" class="btn-x">X</button>
                        <div class="add-box">
                            <form action="/event/createnewbeverage" method="post" enctype="multipart/form-data">
                                <h3>Add Beverage Now</h3>

                                <input type="text" name="name" required placeholder="Enter Beverage Name" class="box" maxlength="50">

                                <input type="text" name="max_glass" required placeholder="Enter Maximum Glasses" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="number" step="0.01" name="per_glass_price" required placeholder="Enter Price per Glass" class="box">

                                <input type="text" name="max_bottle" required placeholder="Enter Maximum Bottles" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="number" step="0.01" name="per_bottle_price" required placeholder="Enter Price per Bottle" class="box">

                                <button type="submit">Add Beverage</button>
                            </form>
                        </div>
                    </div>

                    <div class="add-container" id="edit-container-event">
                        <button id="add-btn-x-edit-event" class="btn-x">X</button>
                        <div class="add-box">
                            <form action="/event/update" method="post" enctype="multipart/form-data">
                                <h3>Update Event</h3>

                                <input type="text" name="event_name" placeholder="Enter Title of the Event" class="box" maxlength="50" required />

                                <input type="text" name="location" placeholder="Enter Location (Optional)" class="box" maxlength="255" required />

                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <label for="is_confirm">Is Confirmed:</label>
                                    <select name="is_confirm" id="is_confirm" class="box" required>
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <label for="status">Event Status:</label>
                                    <select name="status" id="status" class="box" required>
                                        <option value="unpaid" selected>Unpaid</option>
                                        <option value="processing">Processing</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>

                                <textarea name="details" placeholder="Details of the Event" class="box" maxlength="255" required></textarea>

                                <select name="hall_id" id="hall_id" class="box" required>
                                    <option value="">Select The Hall</option>
                                    <?php foreach (getAllHalls() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="food_id" id="food_id" class="box" placeholder="Select The Food">
                                    <option value="">Select The Food</option>
                                    <?php foreach (getAllFoods() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] . ' - Max: ' . $row['max_plates'] . ' - LKR ' . $row['per_plate_price'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select name="beverages_id" id="food_id" class="box" placeholder="Select The Beverages">
                                    <option value="">Select The Beverages</option>
                                    <?php foreach (getAllBeverages() as $row): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['id'] . ' - ' . $row['name'] ?><?php if ($row['max_glass'] != 0): ?> <?= '- Max Glass: ' . $row['max_glass'] ?><?php endif; ?> <?php if ($row['max_bottle'] != 0): ?> <?= '- Max Glass: ' . $row['max_bottle'] ?><?php endif; ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="file" name="event_image_url" accept=".jpg, .jpeg, .png" class="box" />

                                <input type="number" step="0.01" name="event_fee" required placeholder="Enter Price For Event" class="box">

                                <button type="submit">Update Event</button>
                            </form>
                        </div>
                    </div>

                    <div class="add-container" id="edit-container-foods">
                        <button id="edit-btn-x-food" class="btn-x">X</button>
                        <div class="add-box">
                            <form action="/event/foodupdate" method="post" enctype="multipart/form-data">
                                <h3>Update Food</h3>

                                <input type="text" name="name" required placeholder="Enter Food Name" class="box" maxlength="50">

                                <input type="number" step="0.01" name="per_plate_price" required placeholder="Enter Price per Plate" class="box">

                                <input type="text" name="max_plates" required placeholder="Enter Maximum Plates" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <button type="submit">Update</button>
                            </form>


                        </div>
                    </div>

                    <div class="add-container" id="edit-container-beverages">
                        <button id="edit-btn-x-beverages" class="btn-x">X</button>
                        <div class="add-box">
                            <form action="/event/beveragesupdate" method="post" enctype="multipart/form-data">
                                <h3>Update Beverages</h3>

                                <input type="text" name="name" required placeholder="Enter Beverage Name" class="box" maxlength="50">

                                <input type="text" name="max_glass" required placeholder="Enter Maximum Glasses" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="number" step="0.01" name="per_glass_price" required placeholder="Enter Price per Glass" class="box">

                                <input type="text" name="max_bottle" required placeholder="Enter Maximum Bottles" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="number" step="0.01" name="per_bottle_price" required placeholder="Enter Price per Bottle" class="box">

                                <button type="submit">Update</button>
                            </form>


                        </div>
                    </div>

                </div>
                <!-- Event End -->

            <?php elseif ($current_page === "/promotions"): ?>
                <!-- Promotions -->
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Promotions</h1> -->

                    <section class="header-container">
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Promotion</button>
                        </div>
                    </section>
                    <hr />
                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="" method="post">
                                <h3>Add Promotions Now</h3>
                                <input type="hidden" name="req" value="promotion" />
                                <input type="text" name="title" required placeholder="Enter Title of the promotion" class="box" maxlength="100">
                                <input type="datetime-local" name="promotion_date" required placeholder="Enter the promotion date" class="box">
                                <textarea type="text" name="details" required placeholder="details of the promotion" class="box" maxlength="254"></textarea>
                                <input type="file" name="photo" accept=".jpg, .jpeg, .png" required class="box">
                                <button type="submit">Add Promotion</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Promotions End -->

            <?php elseif ($current_page === "/payments"): ?>
                <!-- Payments -->
                
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Payment</h1> -->

                    <section class="header-container">
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Payment</button>
                        </div>
                    </section>
                    <hr />
                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/payments/createnew" method="post" enctype="multipart/form-data">
                                <h3>Add Payment</h3>

                                <select name="client_id" required class="box">
                                    <option value="" disabled>Select The Client</option>
                                    <?php foreach (getAllClients() as $row): ?>
                                    <option value="<?= $row['id']?>"><?= $row['full_name'] ?> - <?= $row['phone_number'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="number" name="amount" required placeholder="Enter the amount" class="box" min="1" step="0.01" autocomplete="off">

                                <select name="status" required class="box">
                                    <option value="" disabled>Select Payment of Status</option>
                                    <option value="pass">Pass</option>
                                    <option value="failed">Failed</option>
                                </select>

                                <button type="submit">Add Payment</button>
                            </form>

                        </div>
                    </div>
                    <div class="table-content-section">
                        <h3>Payment Details</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>                                
                                    <th>User Name</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getAllPayment() as $row): ?>
                                    <tr>
                                        <td style="text-align: center;"><?= $row['id'] ?></td>                                     
                                        <td style="text-align: center;"><?= $row['client_name'] ?> - <?= $row['client_phone'] ?></td>
                                        <td style="text-align: center;"><?= $row['amount'] ?></td>
                                        <td style="text-align: center;"><?= $row['created_at'] ?></td>
                                        <td style="text-align: center;"><?= $row['status'] ?></td>
                                        <td>
                                            <div style="display: flex; gap: 10px; justify-content: center;">
                                                <button data-payment-id="<?= $row['id'] ?>" onclick="handlePaymentEdit(event)"><i class="fa fa-edit"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="add-container" id="edit-container-payment">
                        <button id="add-btn-x-edit-payment" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/payments/update" method="post" enctype="multipart/form-data">
                                <h3>Edit Payment</h3>

                                <select name="client_id" required class="box">
                                    <option value="" disabled>Select The Client</option>
                                    <?php foreach (getAllClients() as $row): ?>
                                    <option value="<?= $row['id']?>"><?= $row['full_name'] ?> - <?= $row['phone_number'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="text" name="amount" required placeholder="Enter the amount" class="box" min="1" >

                                <select name="status" required class="box">
                                    <option value="" disabled>Select Payment of Status</option>
                                    <option value="pass">Pass</option>
                                    <option value="failed">Failed</option>
                                </select>
                                <button type="submit">Update Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    async function handlePaymentEdit(event) {
                            const paymentID = event.currentTarget.getAttribute('data-payment-id');
                            const baseURL = window.location.origin + window.location.pathname + '/api/getpayment/' + paymentID;
                            const editFormContainer = document.getElementById("edit-container-payment");

                            editFormContainer.style.display = 'flex';
                            document.getElementById('add-btn-x-edit-payment').addEventListener('click', function() {
                                this.parentElement.style.display = 'none';
                            });
                            const response = await fetch(baseURL, {
                                method: "GET",
                            });
                            if(response.ok){
                                const data = await response.json();
                                editFormContainer.getElementsByTagName('form')[0].action = `${window.location.origin}/payments/update/${paymentID}`;
                                const listOfInputs = { client_id: 'select', amount: 'input', status: 'select'};
                                for(let keynam in listOfInputs){
                                    editFormContainer.querySelector(`${listOfInputs[keynam]}[name="${keynam}"]`).value = data['payments'][`${keynam}`];
                                }
                            }
                        }
                </script>
                <!-- Payments End -->

            <?php elseif ($current_page === "/halls"): ?>
                <!-- Halls -->
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Halls</h1> -->

                    <section class="header-container">
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Hall</button>
                        </div>
                    </section>
                    <hr />
                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/halls/createnew" method="post" enctype="multipart/form-data">
                                <h3>Add Hall</h3>

                                <input type="text" name="number" required placeholder="Enter Hall Number" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="text" name="name" required placeholder="Enter Hall Name" class="box" maxlength="50">

                                <input type="text" name="number_of_tables" required placeholder="Enter Number of Tables" class="box" min="1" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="text" name="capacity" required placeholder="Enter Hall Capacity" class="box" min="1" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <textarea name="details" required placeholder="Enter Hall Details" class="box" maxlength="256"></textarea>

                                <button type="submit">Add Hall</button>
                            </form>

                        </div>
                    </div>
                    <div class="table-content-section">
                        <h3>Hall Details</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Number</th>
                                    <th>Name</th>
                                    <th>Number of Tables</th>
                                    <th>Capacity</th>
                                    <th>Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getAllHalls() as $row): ?>
                                    <tr>
                                        <td style="text-align: center;"><?= $row['id'] ?></td>
                                        <td style="text-align: center;"><?= $row['number'] ?></td>
                                        <td style="text-align: center;"><?= $row['name'] ?></td>
                                        <td style="text-align: center;"><?= $row['number_of_tables'] ?></td>
                                        <td style="text-align: center;"><?= $row['capacity'] ?></td>
                                        <td style="text-align: center;"><?= $row['details'] ?></td>
                                        <td>
                                            <div style="display: flex; gap: 10px; justify-content: center;">
                                                <!-- <button><i class="fa fa-eye"></i></button> -->
                                                <button data-hall-id="<?= $row['id'] ?>" oPaymentck="handleHallEdit(event)"><i class="fa fa-edit"></i></button>
                                                <button data-hall-id="<?= $row['id'] ?>" onclick="handleHallDelete(event)"><i class="fa fa-trash-alt"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="add-container" id="edit-container-hall">
                        <button id="add-btn-x-edit-hall" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/halls/update" method="post" enctype="multipart/form-data">
                                <h3>Edit Hall</h3>

                                <input type="text" name="number" required placeholder="Enter Hall Number" class="box" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="text" name="name" required placeholder="Enter Hall Name" class="box" maxlength="50">

                                <input type="text" name="number_of_tables" required placeholder="Enter Number of Tables" class="box" min="1" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <input type="text" name="capacity" required placeholder="Enter Hall Capacity" class="box" min="1" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;">

                                <textarea name="details" required placeholder="Enter Hall Details" class="box" maxlength="256"></textarea>

                                <button type="submit">Update Hall</button>
                            </form>

                        </div>
                    </div>
                </div>
                <script>
                    function handleHallDelete(event) {
                        const hallID = event.currentTarget.getAttribute('data-hall-id');
                        const baseURL = window.location.origin + window.location.pathname + '/delete/' + hallID;
                        fetch(baseURL, {
                            method: "DELETE"
                        });
                        window.location.reload();
                    }

                    async function handleHallEdit(event) {
                            const hallID = event.currentTarget.getAttribute('data-hall-id');
                            const baseURL = window.location.origin + window.location.pathname + '/api/gethall/' + hallID;
                            const editFormContainer = document.getElementById("edit-container-hall");

                            editFormContainer.style.display = 'flex';
                            document.getElementById('add-btn-x-edit-hall').addEventListener('click', function() {
                                this.parentElement.style.display = 'none';
                            });

                            const response = await fetch(baseURL, {
                                method: "GET",
                            });
                            if(response.ok){
                                const data = await response.json();
                                editFormContainer.getElementsByTagName('form')[0].action = `${window.location.origin}/halls/update/${hallID}`;
                                const listOfInputs = { number: 'input', name: 'input', number_of_tables: 'input', capacity: 'input', details: 'textarea'};
                                for(let keynam in listOfInputs){
                                    editFormContainer.querySelector(`${listOfInputs[keynam]}[name="${keynam}"]`).value = data['hall'][`${keynam}`];
                                }
                            }
                        }
                </script>
                <!-- Halls End -->

            <?php elseif ($current_page === "/services"): ?>
                <!-- Services -->
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Services</h1> -->

                    <section class="header-container">
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Services</button>
                        </div>
                    </section>
                    <hr />
                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="" method="post">
                                <h3>Add Service Now</h3>
                                <input type="hidden" name="req" value="service" />
                                <input type="text" name="title" required placeholder="Enter Title of the Service" class="box" maxlength="100">
                                <textarea type="text" name="details" required placeholder="details of the service" class="box" maxlength="254"></textarea>
                                <input type="file" name="photo" accept=".jpg, .jpeg, .png" required class="box">
                                <button type="submit">Add Service</button>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- Services End -->
            <?php elseif ($current_page === "/clients"): ?>
                <!-- Clients -->
                <div class="content-container">
                    <!-- <h1 style="text-align: center;">Clients</h1> -->

                    <section class="header-container">
                        <div class="button-container">
                            <button id="btn-add" class="btn add" onclick="handleOpenAddForm()">Add A New Client</button>
                        </div>
                    </section>
                    <hr />
                    <div class="add-container">
                        <button id="add-btn-x" class="btn-x">X</button>
                        <div class="add-box">

                            <form action="/clients/createnew" method="post" enctype="multipart/form-data">
                                <h3>Add New Clients</h3>
                                <input type="text" name="full_name" placeholder="Enter Full Name" class="box" maxlength="100" required>
                                <input type="text" name="phone_number" placeholder="Enter Phone Number" class="box" maxlength="11" onkeypress="if (isNaN(String.fromCharCode(event.keyCode))) return false;" required>
                                <input type="email" name="email" placeholder="Enter Email" class="box" maxlength="100" required>
                                <input type="address" name="address" placeholder="Enter Address" class="box" maxlength="100" required>
                                <button type="submit">Add Client</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-content-section">
                        <h3>Clients Details</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Emails</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getAllClients() as $row): ?>
                                    <tr>
                                        <td style="text-align: center;"><?= $row['id'] ?></td>
                                        <td style="text-align: center;"><?= $row['full_name'] ?></td>
                                        <td style="text-align: center;"><?= $row['phone_number'] ?></td>
                                        <td style="text-align: center;"><?= $row['email'] ?></td>
                                        <td style="text-align: center;"><?= $row['address'] ?></td>
                                        <td>
                                            <div style="display: flex; gap: 10px; justify-content: center;">
                                                <!-- <button><i class="fa fa-eye"></i></button> -->
                                                <button data-hall-id="<?= $row['id'] ?>" oPaymentck="handleHallEdit(event)" title="Edit"><i class="fa fa-edit"></i></button>
                                                <button data-hall-id="<?= $row['id'] ?>" onclick="handleHallDelete(event)" title="Delete"><i class="fa fa-trash-alt"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- Clients End -->
            
                <?php else: ?>
                <div class="content-container">
                    <h1 style="text-align: center;">404 NOT FOUND</h1>
                </div>
            <?php endif; ?>



        </div>
    </div>

    <?php endif;?>
    <script>
        function handleOpenAddForm() {
            document.getElementsByClassName("add-container")[0].style.display = 'flex';
            document.getElementById('add-btn-x').addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        }

        function handleOpenAddFormFood() {
            document.getElementById("add-container-food").style.display = 'flex';
            document.getElementById('add-btn-x-food').addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        }

        function handleOpenAddFormBeverage() {
            document.getElementById("add-container-beverage").style.display = 'flex';
            document.getElementById('add-btn-x-beverage').addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        }
    </script>

    <script src="./js/script.js"></script>
</body>

</html>