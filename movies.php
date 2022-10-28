<?php

function route($method, $urlData, $formData)
{

    $link = mysqli_connect("localhost", "root", "", "vue");
    if (!$link) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(array(
            'error' => mysqli_connect_errno()." - " . mysqli_connect_error()
        ));
    }


    if ($method === 'GET' && count($urlData) === 0) {
        $query = "SELECT * FROM movies";
        $result = mysqli_query($link, $query);
        $data = [];
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$i] = $row;
            $i++;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        return;
    }

    if ($method === 'GET' && count($urlData) === 1 && is_numeric($urlData[0])) {
        $id = (int)$urlData[0];
        $query = "SELECT * FROM movies where id = " . $id;
        $result = mysqli_query($link, $query);
        
        echo json_encode(mysqli_fetch_assoc($result), JSON_UNESCAPED_UNICODE);
        return;
    }


    if ($method === 'POST') {

        $query = "INSERT into movies (title, director) values ('" . mysqli_real_escape_string($link, $formData['title']) . "', '" . mysqli_real_escape_string($link, $formData['director']) . "')";
        $result = mysqli_query($link, $query);

        if ($result) echo json_encode(array(
            'status' => 'created',
            'id' => mysqli_insert_id($link)
        ));
        else echo json_encode(array(
            'error' => mysqli_error($link)
        ));

        return;
    }


    if (($method === 'PUT' && count($urlData) === 1) || ($method === 'PATCH' && count($urlData) === 1)) {

        $id = $urlData[0];

        $query = "UPDATE movies set title = '" . $formData['title'] . "', director = '" . $formData['director'] . "' where id = " . $id;
        $result = mysqli_query($link, $query);


        if ($result) echo json_encode(array(
            'status' => 'updated',
            'id' => $id
        ));
        else echo json_encode(array(
                'error' => mysqli_error($link)
             ));
        return;
    }


    if ($method === 'DELETE' && count($urlData) === 1) {

        $id = $urlData[0];


        $query = "DELETE from movies where id = " . $id;
        $result = mysqli_query($link, $query);


        if ($result) echo json_encode(array(
            'status' => 'deleted',
            'id' => $id
        ));
        else echo json_encode(array(
            'error' => mysqli_error($link)
        ));

        return;
    }

    mysqli_close($link);

    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));
}
