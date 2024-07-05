<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PWOD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-3">PWOT</h1>
    <form action="index.php" method="post" class="row g-3 mt-3 align-items-end">
        <div class="col-auto">
            <label for="arrived_at">Arrived at</label>
            <input type="datetime-local" name="arrived_at" class="form-control" id="arrived_at">
        </div>
        <div class="col-auto">
            <label for="leaved_at">Leaved at</label>
            <input type="datetime-local" name="leaved_at" class="form-control" id="leaved_at">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col-auto">
            <a href="download.php" class="btn btn-success">Export</a>
        </div>
    </form>
    <table class="table table-hover table-striped mt-5">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Arrived at</th>
            <th scope="col">Leaved at</th>
            <th scope="col">Required work off</th>
            <th scope="col">Worked off</th>
        </tr>
        </thead>
        <tbody>

        <?php
        global $workDay;
        $limit = 5;
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($currentPage - 1) * $limit;
        $workDayList = $workDay->pdo->query("SELECT * FROM daily LIMIT $offset, $limit")->fetchAll(PDO::FETCH_ASSOC);

        $totalRecordsCount = $workDay->pdo->query("SELECT COUNT(*) FROM daily")->fetchColumn();
        $pages = ceil($totalRecordsCount / $limit);
        ?>

        <?php
        if (!empty($workDayList)) :
        foreach ($workDayList as $day) : ?>
            <tr class="<?php echo $day['worked_off'] ? 'table-success' : '' ?>">
                <th scope="row"><?php echo $day['id'] ?></th>
                <td><?php echo $day['arrived_at'] ?></td>
                <td><?php echo $day['leaved_at'] ?></td>
                <td><?php echo $workDay->getHumanReadableDiff($day['required_work_off']) ?></td>
                <td>
                    <?php
                    if ($day['worked_off']) {
                        ?>
                        <input type="checkbox" checked disabled>
                        <?php
                    } else {
                        ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal<?php echo $day['id']; ?>">
                            Done
                        </button>

                        <div class="modal fade" id="exampleModal<?php echo $day['id']; ?>" tabindex="-1"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to mark this workday as done?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="index.php" method="post">
                                            <input type="hidden" name="done" value="<?php echo $day['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
        <tr>
            <th colspan="3">Total work off hours</th>
            <th><?php echo $workDay->getTotalWorkOffTime(); ?></th>
            <th></th>
        </tr>
        </tfoot>
        <?php else : ?>
            <tr>
                <td colspan="5">
                    <div class="text-center">No data to show</div>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="container">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $currentPage - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php
            for ($page = 1; $page <= $pages; $page++) : ?>
                <li class="page-item <?php echo $page == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?php echo $page ?>"><?php echo $page; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $currentPage + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>
</html>