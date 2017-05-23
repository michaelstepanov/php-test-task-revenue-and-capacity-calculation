<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
                <h2>Revenue and Capacity Calculation</h2>
                <form action="" method="post">
                    <div class="form-group <?php if ($_POST && $error) {echo 'has-error';} ?>">
                        <label for="exampleInputEmail1">Set a month and a year in the format: YYYY-MM</label>
                        <input type="text"
                               required
                               name="year-and-month"
                               class="form-control"
                               id="exampleInputEmail1"
                               value="<?php echo isset($_POST['year-and-month']) ? $_POST['year-and-month'] : '' ?>"
                               placeholder="YYYY-MM">
                        <?php
                        if ($_POST && $error) {
                            ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </form>
                <?php
                if ($_POST && !$error) {
                    ?>
                    <hr>
                    <h2>Results:</h2>
                    <h4>Revenue: <strong>$<?php echo number_format($revenue); ?></strong></h4>
                    <h4>Unreserved offices: <strong><?php echo $totalCapacity; ?></strong></h4>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>