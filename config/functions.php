<?php

require_once 'dbcon.php';
function getCarSeries($seriesId)
{
    global $conn;

    $query = "SELECT * FROM cars WHERE series_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$seriesId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}
