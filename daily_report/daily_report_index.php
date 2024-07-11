<?php include("../dbconn.php");
$has_children = 1;
$get_pid = 0;
if (isset($_GET["pid"])) {
    $get_pid = $_GET["pid"];
}
include "../layouts/session.php"; ?>
<?php include "../layouts/main.php"; ?>
<?php
if (isset($_GET["method"]) && $_GET["method"] == "delete" && isset($_GET["id"])) {
    //daily_report_sql_del
    $tablename = "daily_report";
    $id = $_GET["id"];
    $sql = "update $tablename set is_delete = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        echo '<script type="text/javascript">';
        echo 'window.location.href="daily_report_index.php";';
        echo '</script>';
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<head>
    <?php includeFileWithVariables(
        "../layouts/title-meta.php",
        ["title" => "daily_report"]
    ); ?>
    <?php include "../layouts/head-css.php"; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include "../layouts/menu.php"; ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php if (isset($_GET["method"])) {
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            if (empty($_POST['id'])) {
                                //daily_report_sql_insert
                                $tablename = "daily_report";
                                $stmt = $conn->prepare("insert INTO $tablename (`name`,`datetime_create`,`datetime_update`,`pid`) VALUES (?,?,?,?)");
                                if (false === $stmt) {
                                    die("Prepare failed: " . $conn->error);
                                }
                                $stmt->bind_param("sssi", $name, $datetime_create, $datetime_update, $pid);
                                $name = $_POST['name'];
                                $datetime_create = date("Y-m-d H:i:s", time());
                                $datetime_update = date("Y-m-d H:i:s", time());
                                $pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
                                if ($stmt->execute()) {
                                    echo '<script type="text/javascript">';
                                    echo 'window.location.href="daily_report_index.php?pid=' . $pid . '";';
                                    echo '</script>';
                                    $stmt->close();
                                    exit();
                                } else {
                                    echo "Error: " . $stmt->error;
                                }
                            } else {
                                // 获取从表单提交的数据
                                $id = $_POST['id'] ?? $_GET['id'] ?? 'default'; // 优先使用POST数据，如果没有则尝试从GET数据中获取
                                $name = $_POST['name'] ?? null;
                                $is_publish = $_POST['is_publish'] ?? 0;
                                $sort = $_POST['sort'] ?? 0;
                                $pid = $_POST['pid'] ?? 0;
                                $datetime_update = $_POST['datetime_update'] ?? date("Y-m-d H:i:s");
                                $num_year = $_POST['num_year'] ?? null;
                                $num_month = $_POST['num_month'] ?? null;
                                $num_week = $_POST['num_week'] ?? null;
                                $is_even = $_POST['is_even'] ?? 0;
                                $content = $_POST['content'] ?? null;
                                $feedback = $_POST['feedback'] ?? null;
                                //daily_report_sql_update1
                                $updateParts = [];
                                $hasFeaturedImage = false;
                                $newFilefeatured_image = null;
                                $originfeatured_image = null;
                                if (isset($_FILES["featured_image"]) && $_FILES["featured_image"]["error"] == 0) {
                                    $file = $_FILES["featured_image"];
                                    $originfeatured_image = $file["name"];
                                    $uploadPath = "../uploads/";
                                    $id = $_GET["id"] ?? 'default';
                                    $extension = pathinfo($originfeatured_image, PATHINFO_EXTENSION);
                                    $newFilefeatured_image = "daily_report_featured_image_" . $id . "." . $extension;
                                    $filePath = $uploadPath . $newFilefeatured_image;
                                    if (!is_dir($uploadPath) && !mkdir($uploadPath, 0777, true)) {
                                        echo json_encode(["error" => "create error"]);
                                        exit;
                                    }
                                    if (move_uploaded_file($file["tmp_name"], $filePath)) {
                                        $hasFeaturedImage = true;
                                    }
                                }
                                if ($hasFeaturedImage) {
                                    $updateParts['featured_image'] = $newFilefeatured_image;
                                    $updateParts['featured_image_ori'] = $originfeatured_image;
                                }
                                //daily_report_sql_upload
                                //
                                $tablename = "daily_report";
                                $updateParts['num_year'] = $num_year;
                                $updateParts['num_month'] = $num_month;
                                $updateParts['num_week'] = $num_week;
                                $updateParts['is_even'] = $is_even;
                                $updateParts['content'] = $content;
                                $updateParts['feedback'] = $feedback;
                                $updateParts['name'] = $name;
                                $updateParts['datetime_update'] = $datetime_update;
                                $updateParts['is_publish'] = $is_publish;
                                $updateParts['pid'] = $pid;
                                $updateParts['sort'] = $sort;
                                //daily_report_sql_update2
                                $sql = "update daily_report set ";
                                $setStatements = [];
                                foreach ($updateParts as $key => $value) {
                                    $setStatements[] = "`$key` = ?";
                                }
                                $sql .= implode(", ", $setStatements) . " WHERE `id` = ?";
                                $stmt = $conn->prepare($sql);
                                if (!$stmt) {
                                    die("Error: " . $conn->error);
                                }
                                $types = str_repeat("s", count($updateParts)) . "i";
                                $params = array_values($updateParts);
                                $params[] = $id;
                                $stmt->bind_param($types, ...$params);
                                $stmt->execute();
                            }
                        }
                        if ($_GET['method'] != "add") {
                            $num_year = "";
                            $num_month = "";
                            $num_week = "";
                            $is_even = "";
                            $content = "";
                            $feedback = "";
                            $tablename = "daily_report";
                            $id = $_GET['id'];
                            $sql = "select * FROM $tablename WHERE id = '$id'";
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $name = $row['name'];
                                    $sort = $row['sort'];
                                    $pid = $row['pid'];
                                    $is_publish = $row["is_publish"];
                                    $checkedStatus = ($is_publish == "1") ? "checked" : "";
                                    $featured_image = $row['featured_image'];
                                    $featured_image_ori = $row['featured_image_ori'];
                                    $num_year = $row['num_year'];
                                    $num_month = $row['num_month'];
                                    $num_week = $row['num_week'];
                                    $is_even = $row['is_even'];
                                    $is_evenStatus = ($is_even == '1') ? 'checked' : '';
                                    $content = $row['content'];
                                    $feedback = $row['feedback'];
                                    //daily_report_sql_view
                                }
                            } else {
                                echo "No records found";
                            }
                            $conn->close();
                        }
                        if ($_GET['method'] == "add") {
                    ?>
                            <form method="post" id="daily_report_form" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="input-group mb-3" id="div_name">
                                            <span id="div_name" class="input-group-text">Name</span>
                                            <input required type="text" class="form-control" id="name" name="name" placeholder="name" value="">
                                            <a class="btn btn-outline-primary d-none" id="div_a_name" target="_blank" href="https://www.google.com/search?q=name">Jump</a>
                                        </div>
                                        <div class="text-end">
                                            <button id="submit_button" type="submit" class="btn btn-success" accesskey="s" onclick="disableButton()">ADD</button>
                                            <a href="daily_report_list.php" type="button" class="btn btn-danger waves-effect waves-light" accesskey="b">Back</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } else {
                        ?>
                            <form method="post" id="daily_report_form" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <?php if ($_GET['method'] == "edit") {
                                            echo "<input type='hidden' name='id' value='" . $_GET["id"] . "'>";
                                        } ?>
                                        <div class="input-group mb-3" id="div_name">
                                            <span id="div_name" class="input-group-text">Name</span>
                                            <input type="text" <?php if ($_GET["method"] == "view") {
                                                                    echo "disabled";
                                                                } ?> class="form-control" id="name" name="name" placeholder="name" value="<?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                    echo "";
                                                                                                                                                                                                } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                    echo htmlspecialchars($name);
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    echo htmlspecialchars($name);
                                                                                                                                                                                                } ?>">
                                            <a class="btn btn-outline-primary d-none" id="div_a_name" target="_blank" href="https://www.google.com/search?q=name">Jump</a>
                                        </div>
                                        <div class="input-group mb-3 <?php if ($has_children == 0) {
                                                                            echo 'd-none';
                                                                        } else {
                                                                            echo '';
                                                                        } ?>" id="div_pid"><span id="div_span_pid" class="input-group-text">Pid</span><input <?php if ($_GET["method"] == "view") {
                                                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                                                } ?> type="number" class="form-control" placeholder="Pid" id="pid" name="pid" value="<?php if ($_GET["method"] == "add") {
                                                                                            echo $get_pid;
                                                                                        } else if ($_GET["method"] == "edit") {
                                                                                            echo htmlspecialchars($pid);
                                                                                        } else {
                                                                                            echo htmlspecialchars($pid);
                                                                                        } ?>"><a class="btn btn-outline-primary d-none" id="div_a_pid" target="_blank" href="https://www.google.com/search?q=pid">Jump</a></div>
                                        <div class="input-group mb-3 d-none" id="div_sort"><span id="div_span_sort" class="input-group-text">排序</span><input <?php if ($_GET["method"] == "view") {
                                                                                                                                                                    echo "disabled";
                                                                                                                                                                } ?> type="number" class="form-control" placeholder="排序" id="sort" name="sort" value="<?php if ($_GET["method"] == "add") {
                                                                                            echo "";
                                                                                        } else if ($_GET["method"] == "edit") {
                                                                                            echo htmlspecialchars($sort);
                                                                                        } else {
                                                                                            echo htmlspecialchars($sort);
                                                                                        } ?>"><a class="btn btn-outline-primary d-none" id="div_a_sort" target="_blank" href="https://www.google.com/search?q=sort">Jump</a></div>                                        
                                        <div class="input-group mb-3" id="div_num_year"><span id="div_span_num_year"   class="input-group-text">num_year</span><input <?php if ($_GET["method"] == "view") {
                                                                                                                                                                            echo "disabled";
                                                                                                                                                                        } ?> type="number" class="form-control" placeholder="num_year" id="num_year" name="num_year" value="<?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                                                                                                                                                    echo "";
                                                                                                                                                                                                                                                                                                                                } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                                                                                                                                                    echo htmlspecialchars($num_year);
                                                                                                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                                                                                                    echo htmlspecialchars($num_year);
                                                                                                                                                                                                                                                                                                                                } ?>"><a class="btn btn-outline-primary d-none" id="div_a_num_year" target="_blank" href="https://www.google.com/search?q=num_year">Jump</a></div>
                                        <div class="input-group mb-3" id="div_num_month"><span id="div_span_num_month"   class="input-group-text">num_month</span><input <?php if ($_GET["method"] == "view") {
                                                                                                                                                                                echo "disabled";
                                                                                                                                                                            } ?> type="number" class="form-control" placeholder="num_month" id="num_month" name="num_month" value="<?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                                                                                                                                                        echo "";
                                                                                                                                                                                                                                                                                                                                    } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                                                                                                                                                        echo htmlspecialchars($num_month);
                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                        echo htmlspecialchars($num_month);
                                                                                                                                                                                                                                                                                                                                    } ?>"><a class="btn btn-outline-primary d-none" id="div_a_num_month" target="_blank" href="https://www.google.com/search?q=num_month">Jump</a></div>
                                        <div class="input-group mb-3" id="div_num_week"><span id="div_span_num_week"   class="input-group-text">num_week</span><input <?php if ($_GET["method"] == "view") {
                                                                                                                                                                            echo "disabled";
                                                                                                                                                                        } ?> type="number" class="form-control" placeholder="num_week" id="num_week" name="num_week" value="<?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                                                                                                                                                    echo "";
                                                                                                                                                                                                                                                                                                                                } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                                                                                                                                                    echo htmlspecialchars($num_week);
                                                                                                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                                                                                                    echo htmlspecialchars($num_week);
                                                                                                                                                                                                                                                                                                                                } ?>"><a class="btn btn-outline-primary d-none" id="div_a_num_week" target="_blank" href="https://www.google.com/search?q=num_week">Jump</a></div>
                                        <div class="form-check mb-2" id="div_check_is_even"><input class="form-check-input" type="checkbox" id="is_even" name="is_even" value="1" <?php echo isset($is_evenStatus) ? $is_evenStatus : ""; ?>><label class="form-check-label" for="is_even">is_even</label></div>
                                        <div class="input-group mb-3" id="div_content"><span id="div_span_content" class="input-group-text">content</span><textarea <?php if ($_GET["method"] == "view") {
                                                                                                                                                                        echo "disabled";
                                                                                                                                                                    } ?> type="text" class="form-control" placeholder="content" id="content" name="content" value="" rows="5"><?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                                                                                                                                                        echo "";
                                                                                                                                                                                                                                                                                                                                    } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                                                                                                                                                        echo htmlspecialchars($content);
                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                        echo htmlspecialchars($content);
                                                                                                                                                                                                                                                                                                                                    } ?></textarea><a class="btn btn-outline-primary d-none" id="div_a_content" target="_blank" href="https://www.google.com/search?q=content">Jump</a></div>
                                        <div class="input-group mb-3" id="div_feedback"><span id="div_span_feedback" class="input-group-text">feedback</span><textarea <?php if ($_GET["method"] == "view") {
                                                                                                                                                                            echo "disabled";
                                                                                                                                                                        } ?> type="text" class="form-control" placeholder="feedback" id="feedback" name="feedback" value="" rows="5"><?php if ($_GET["method"] == "add") {
                                                                                                                                                                                                                                                                                                                                            echo "";
                                                                                                                                                                                                                                                                                                                                        } else if ($_GET["method"] == "edit") {
                                                                                                                                                                                                                                                                                                                                            echo htmlspecialchars($feedback);
                                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                                            echo htmlspecialchars($feedback);
                                                                                                                                                                                                                                                                                                                                        } ?></textarea><a class="btn btn-outline-primary d-none" id="div_a_feedback" target="_blank" href="https://www.google.com/search?q=feedback">Jump</a></div>
                                        <div class="form-check mb-2" id="div_check_is_publish">
                                            <input class="form-check-input" type="checkbox" id="is_publish" name="is_publish" value="1" <?php echo isset($checkedStatus) ? $checkedStatus : ""; ?>>
                                            <label class="form-check-label" for="is_publish">
                                                is_publish
                                            </label>
                                        </div>
                                        <?php if ($_GET['method'] != 'view' && $_GET["method"] != "add") { ?>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" id="featured_image" name="featured_image">
                                                <label class="input-group-text" for="featured_image">featured_image Upload</label>
                                            </div>
                                        <?php } ?>
                                        <?php if (isset($featured_image)) { ?>
                                            <div class="row mt-2">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <img src="/uploads/<?php echo $featured_image; ?>" class="img-fluid" alt="<?php echo $featured_image_ori; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="text-end"><?php if ($_GET['method'] == "add") {
                                                                    echo '<button type="submit" class="btn btn-success" accesskey="s">ADD</button>';
                                                                } else if ($_GET['method'] == "edit") {
                                                                    echo '<button type="submit" class="btn btn-success" accesskey="s">edit</button>';
                                                                } else {
                                                                    echo '<a href="daily_report_index.php?id=' . $_GET["id"] . '&method=edit"><button type="button" class="btn btn-success waves-effect waves-light">go edit</button></a>';
                                                                } ?>
                                            <a href="daily_report_index.php" type="button" class="btn btn-danger waves-effect waves-light" accesskey="b">Back</a>
                                        </div>
                                    </div> 
                                </div>
                            </form>
                        <?php
                        }
                    } else {
                        includeFileWithVariables("../layouts/page-title.php", [
                            "pagetitle" => "首页",
                            "title" => "项目列表",
                            "url" => "daily_report_index.php",
                        ]); ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h5 class="card-title mb-0">daily_report</h5>
                                        <div>
                                            <a href="daily_report_index.php?pid=<?= $get_pid ?>&method=add" type="button" class="btn btn-success add-btn"><i class="ri-add-line align-bottom me-1"></i>
                                                add</a>
                                            <?php if (isset($_GET["pid"])) { ?>
                                                <a href="daily_report_index.php" type="button" class="btn btn-danger waves-effect waves-light" accesskey="b">Back</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="<?php if (isset($_GET["pid"])) {
                                                        echo "example";
                                                    } else {
                                                        echo "";
                                                    } ?>" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>name</th>
                                                    <th>num_year</th>
                                                    <th>num_month</th>
                                                    <th>num_week</th>
                                                    <th>is_even</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (isset($_GET["pid"])) {
                                                    $tablename = "daily_report";
                                                    if ($has_children == 0) {
                                                        $sql = "select * FROM $tablename WHERE is_delete = 0 order by id desc";
                                                    } else {
                                                        $sql = "select * FROM $tablename WHERE is_delete = 0 and pid=$get_pid order by sort asc, id asc";
                                                    }
                                                    $result = $conn->query($sql);
                                                    if ($result) {
                                                        $rows = $result->fetch_all(MYSQLI_ASSOC);
                                                        foreach ($rows as $row) {
                                                            echo "<tr>";
                                                            if ($has_children == 0) {
                                                                echo "<td>" . $row['id'] . "</td>";
                                                                echo "<td><a href='daily_report_index.php?id=" . $row['id'] . "&method=edit'>" . $row['name'] . "</a></td>";
                                                            } else {
                                                                echo "<td><a href='daily_report_index.php?pid=" . $row['id'] . "'>" . $row['id'] . "</a></td>";
                                                                echo "<td><a href='daily_report_index.php?pid=" . $row['id'] . "'>" . $row['name'] . "</a></td>";
                                                            } ?>
                                                            <td><?= $row['num_year'] ?></td>
                                                            <td><?= $row['num_month'] ?></td>
                                                            <td><?= $row['num_week'] ?></td>
                                                            <td><?= $row['is_even'] ?></td>
                                                            <td>
                                                                <div class=' d-inline-block'>
                                                                    <a href='daily_report_index.php?id=<?= $row['id'] ?>&method=view'><i class='ri-eye-fill align-bottom me-2 text-muted'></i> View</a>
                                                                    <a class='edit-item-btn' href='daily_report_index.php?id=<?= $row['id'] ?>&method=edit'><i class='ri-pencil-fill align-bottom me-2 text-muted'></i> Edit</a>
                                                                    <a class='edit-item-btn' href='daily_report_index.php?id=<?= $row['id'] ?>&method=delete'><i class='ri-delete-bin-fill align-bottom me-2 text-muted'></i> Delete</a>
                                                                </div>
                                                            </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        echo "Error: " . $sql . "<br>" . $conn->error;
                                                    }
                                                    $conn->close();
                                                } else {
                                                    $tablename = "daily_report";
                                                    $sql = "select * FROM $tablename WHERE is_delete = 0 AND pid=0";
                                                    $result = $conn->query($sql);
                                                    function renderRow($row, $level)
                                                    {
                                                        global $conn, $tablename;
                                                        $indent = str_repeat("&nbsp;", $level * 4) . ($level > 0 ? "- " : "&nbsp;&nbsp;");
                                                        ?>
                                                        <tr>
                                                            <td><a href='daily_report_index.php?pid=<?= $row['id'] ?>'><?= $row['id'] ?></a></td>
                                                            <td class='indent' style='padding-left: <?= ($level * 20) ?>px;'><?= $indent ?><a href='daily_report_index.php?pid=<?= $row['id'] ?>'><?= $row['name'] ?></a></td>
                                                            <td><?= $row['num_year'] ?></td>
                                                            <td><?= $row['num_month'] ?></td>
                                                            <td><?= $row['num_week'] ?></td>
                                                            <td><?= $row['is_even'] ?></td>
                                                            <td>
                                                                <div class='d-inline-block'>
                                                                    <a href='daily_report_index.php?id=<?= $row['id'] ?>&method=view'><i class='ri-eye-fill align-bottom icon text-muted'></i> View</a>
                                                                    <a class='edit-item-btn' href='daily_report_index.php?id=<?= $row['id'] ?>&method=edit'><i class='ri-pencil-fill align-bottom icon text-muted'></i> Edit</a>
                                                                    <a class='edit-item-btn' href='daily_report_index.php?id=<?= $row['id'] ?>&method=delete'><i class='ri-delete-bin-fill align-bottom icon text-muted'></i> Delete</a>
                                                                </div>
                                                            </td>
                                                        </tr><?php
                                                                $sub_sql = "select * FROM $tablename WHERE is_delete = 0 AND pid=" . $row['id'];
                                                                $sub_result = $conn->query($sub_sql);
                                                                if ($sub_result && $sub_result->num_rows > 0) {
                                                                    $sub_rows = $sub_result->fetch_all(MYSQLI_ASSOC);
                                                                    foreach ($sub_rows as $sub_row) {
                                                                        renderRow($sub_row, $level + 1);
                                                                    }
                                                                }
                                                            }
                                                            if ($result) {
                                                                $rows = $result->fetch_all(MYSQLI_ASSOC);
                                                                foreach ($rows as $row) {
                                                                    renderRow($row, 0);
                                                                }
                                                            } else {
                                                                echo "<tr><td colspan='4'>Error: " . $conn->error . "</td></tr>";
                                                            }
                                                            $conn->close();
                                                        }
                                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
            <?php include "../layouts/footer.php"; ?>
        </div>
    </div>
    <?php include "../layouts/vendor-scripts.php"; ?>
    <script>
        //id,name,num_year,num_month,num_week,is_even,content,feedback,
        $("#example").DataTable({
            "columnDefs": [{
                    "targets": [0, 1, -1],
                    "visible": true,
                },
                {
                    "targets": "_all",
                    "visible": false,
                }
            ],
            "order": [
                [0, "desc"]
            ],
            "pageLength": 100
        });

        function disableButton() {
            document.getElementById('submit_button').disabled = true;
            document.getElementById('daily_report_form').submit();
        }
    </script>
</body>

</html>