<?php

function pagination($link, $page) {
    $query = 'SELECT count(*) FROM stories';
    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_row($result)) {
        $total = $row[0];
    }

    $page = (int)$page;
    $per_page = 5;
    $last_page = ceil($total/$per_page);

    if ($page > $last_page) {
        $page = $last_page;
    } else if ($page < 1){
        $page = 1;
    };

    if ($page > 1 && $page < $last_page){
        $prev_page = 'http://apivue/api/stories?page=' . ($page - 1);
        $next_page = 'http://apivue/api/stories?page=' . ($page + 1);
    } else if ($page == $last_page) {
        $prev_page = 'http://apivue/api/stories?page=' . ($page - 1);
        $next_page = null;
    } else if ($page == 1) {
        $prev_page = null;
        $next_page = 'http://apivue/api/stories?page=' . ($page + 1);
    } else {
        $prev_page = null;
        $next_page = null;
    };

    $data = [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' =>  $page,
        'last_page' =>  $last_page,
        'next_page_url' =>  $next_page,
        'prev_page_url' =>  $prev_page,
    ];

    return $data;
};

    

function route($method, $urlData, $formData)
{

    // Підключення до бази
    $link = mysqli_connect("localhost", "root", "", "vue");
    if (!$link) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(array(
            'error' => mysqli_connect_errno()." - " . mysqli_connect_error()
        ));
    }


    // GET api/stoties
    if ($method === 'GET' && count($urlData) === 0) {
        $page = (int)$_GET['page'];
        if (!$page) $page = 1;
        $pagination = pagination($link, $page);
        $from = ($pagination['current_page'] - 1) * $pagination['per_page'];
        $to = $pagination['per_page'];
        $query = "SELECT * FROM stories ORDER BY id limit ".$from.",".$to;
        $result = mysqli_query($link, $query);
        $data = [];
        $stories = [];
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $stories[$i] = $row;
            $i++;
        }

        $data = [
            'data' => $stories, 
            'total' => $pagination['total'], 
            'per_page' => $pagination['per_page'], 
            'current_page' => $pagination['current_page'],
            'last_page' => $pagination['last_page'],
            'next_page_url' => $pagination['next_page_url'],
            'prev_page_url' => $pagination['prev_page_url'],
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return; 
    }


    // GET api/stoties/{id}
    if ($method === 'GET' && count($urlData) === 1 && is_numeric($urlData[0])) {
        $id = (int)$urlData[0];
        $query = "SELECT * FROM stories where id = " . $id;
        $result = mysqli_query($link, $query);
        
        echo json_encode(mysqli_fetch_assoc($result), JSON_UNESCAPED_UNICODE);
        return;
    }

    // POST /goods
    if ($method === 'POST') {
        // Добавляем товар в базу...
        $query = "INSERT into stories (plot, writer, upvotes) values ('" . mysqli_real_escape_string($link, $formData['plot']) . "', '" . mysqli_real_escape_string($link, $formData['writer']) . "', " . $formData['upvotes'] . ")";
        $result = mysqli_query($link, $query);
        // Выводим ответ клиенту
        if ($result) echo json_encode(array(
            'status' => 'created',
            'id' => mysqli_insert_id($link)
        ));
        else echo json_encode(array(
            'error' => mysqli_error($link)
        ));

        return;
    }

    // Обновление всех данных товара
    // PUT api/stoties/{id}
    if (($method === 'PUT' && count($urlData) === 1) || ($method === 'PATCH' && count($urlData) === 1)) {

        $id = $urlData[0];

        $query = "UPDATE stories set plot = '" . $formData['plot'] . "', writer = '" . $formData['writer'] . "', upvotes = " . $formData['upvotes'] . " where id = " . $id;
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

    // DELETE api/stoties/{id}
    if ($method === 'DELETE' && count($urlData) === 1) {

        $id = $urlData[0];


        $query = "DELETE from stories where id = " . $id;
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
